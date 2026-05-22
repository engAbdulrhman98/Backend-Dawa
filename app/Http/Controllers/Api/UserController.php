<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Hash;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * UserController
 *
 * Handles user management and own profile.
 * Auth logic  → AuthController
 * Notifications → NotificationController
 *
 * Role access:
 *   index   → super-admin only
 *   store   → super-admin (any role) | pharmacy-owner (branch-manager only)
 *   show    → super-admin | own account
 *   update  → super-admin | pharmacy-owner (own pharmacy) | own account
 *   destroy → super-admin only
 *   me      → any authenticated user (own profile)
 */
class UserController extends Controller
{
    /**
     * List all users.
     * GET /api/v1/users
     * Auth: super-admin only
     *
     * Filters:
     *   ?filter[role]=branch-manager
     *   ?filter[of_pharmacy]=1
     *   ?filter[of_branch]=3
     *
     * Includes:
     *   ?include=pharmacy,branch
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        abort_unless(
            $request->user()->isSuperAdmin(),
            403,
            __('user.messages.unauthorized')
        );

        $users = QueryBuilder::for(User::class)
            ->allowedFilters([
                AllowedFilter::scope('of_pharmacy', 'ofPharmacy'),
                AllowedFilter::scope('of_branch', 'ofBranch'),
                AllowedFilter::callback('role', fn($q, $v) => $q->role($v)),
            ])
            ->allowedIncludes(['pharmacy', 'branch'])
            ->defaultSort('-created_at')
            ->paginate($request->integer('per_page', 100))
            ->withQueryString();

        return UserResource::collection($users);
    }

    /**
     * Create a staff user.
     * POST /api/v1/users
     * Auth: super-admin | pharmacy-owner (branch-manager for own pharmacy only)
     *
     * Clients register via POST /api/v1/auth/register — not here.
     * Authorization and field rules are in StoreUserRequest.
     */
    public function store(StoreUserRequest $request): UserResource
    {
        $v = $request->validated();

        $user = User::create([
            'name'        => $v['name'],
            'email'       => $v['email'],
            'password'    => Hash::make($v['password']),
            'pharmacy_id' => $v['pharmacy_id'] ?? null,
            'branch_id'   => $v['branch_id']   ?? null,
        ]);

        $user->assignRole($v['role']);
        $user->load(['pharmacy', 'branch']);

        return new UserResource($user);
    }

    /**
     * Show a specific user.
     * GET /api/v1/users/{user}
     * Auth: super-admin or the user themselves
     */
    public function show(Request $request, User $user): UserResource
    {
        abort_unless(
            $request->user()->isSuperAdmin() ||
                (int) $request->user()->id === (int) $user->id,
            403,
            __('user.messages.unauthorized')
        );

        $user->loadMissing(['pharmacy', 'branch']);

        return new UserResource($user);
    }

    /**
     * Update a user.
     * PUT/PATCH /api/v1/users/{user}
     * Auth: super-admin | pharmacy-owner (own pharmacy) | user themselves
     *
     * Only super-admin can change: role, pharmacy_id, branch_id
     * Anyone can change own: name, email, password
     */
    public function update(UpdateUserRequest $request, User $user): UserResource
    {
        $v = $request->validated();

        if ($request->user()->isSuperAdmin()) {
            if (isset($v['role'])) {
                $user->syncRoles([$v['role']]);
            }
            if (array_key_exists('pharmacy_id', $v)) {
                $user->pharmacy_id = $v['pharmacy_id'];
            }
            if (array_key_exists('branch_id', $v)) {
                $user->branch_id = $v['branch_id'];
            }
        }

        if (isset($v['name']))     $user->name     = $v['name'];
        if (isset($v['email']))    $user->email    = $v['email'];
        if (isset($v['password'])) $user->password = Hash::make($v['password']);

        $user->save();
        $user->load(['pharmacy', 'branch']);

        return new UserResource($user);
    }

    /**
     * Delete a user.
     * DELETE /api/v1/users/{user}
     * Auth: super-admin only
     *
     * Super-admin cannot delete themselves.
     */
    public function destroy(Request $request, User $user): JsonResponse
    {
        abort_unless(
            $request->user()->isSuperAdmin(),
            403,
            __('user.messages.unauthorized')
        );

        abort_if(
            (int) $request->user()->id === (int) $user->id,
            422,
            __('user.messages.cannot_delete_self')
        );

        $user->delete();

        return response()->json([
            'message' => __('user.messages.deleted'),
        ]);
    }

    /**
     * Get own profile.
     * GET /api/v1/me
     * Auth: any authenticated user (all roles)
     *
     * Returns full UserResource:
     *   role booleans, unread_notifications_count,
     *   loaded pharmacy + branch relationships.
     */
    public function me(Request $request): UserResource
    {
        return new UserResource(
            $request->user()->loadMissing(['pharmacy', 'branch'])
        );
    }

    /**
     * Update own profile.
     * PUT/PATCH /api/v1/me
     * Auth: any authenticated user (all roles)
     *
     * Routes to update() with the authenticated user as the target.
     * Role, pharmacy_id, branch_id changes are blocked unless super-admin.
     */
    public function updateMe(UpdateUserRequest $request): UserResource
    {
        return $this->update($request, $request->user());
    }
}
