<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterClientRequest;
use App\Http\Resources\UserResource;
use App\Models\Branch;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * AuthController
 *
 * Handles authentication only:
 *   login      → all roles, unified endpoint
 *   register   → client self-registration only
 *   logout     → current device
 *   logout-all → all devices
 *   refresh    → rotate token
 *
 * Profile management (GET /me, PUT /me) → UserController
 * Notifications                         → NotificationController
 *
 * Role creation flow:
 *   super-admin    → AdminSeeder only (never public)
 *   pharmacy-owner → POST /api/v1/users by super-admin
 *   branch-manager → POST /api/v1/users by super-admin or pharmacy-owner
 *   client         → POST /api/v1/auth/register (public)
 */
class AuthController extends Controller
{
    // ──────────────────────────────────────────
    // Public — no auth required
    // ──────────────────────────────────────────

    /**
     * Unified login for ALL roles.
     * POST /api/v1/auth/login
     *
     * Body: { email, password }
     *
     * Response:
     *   token    → Sanctum plain-text token — store in mobile secure storage
     *   user     → UserResource (role, is_* booleans, pharmacy, branch)
     *   redirect → frontend routing hint per role
     *
     * Redirect values:
     *   admin.dashboard    → super-admin
     *   pharmacy.dashboard → pharmacy-owner
     *   branch.dashboard   → branch-manager
     *   client.home        → client
     */
    // public function login(LoginRequest $request): JsonResponse
    // {
    //     $user = User::where('email', $request->email)->first();

    //     if (!$user || !Hash::check($request->password, $user->password)) {
    //         return response()->json([
    //             'message' => __('auth.messages.invalid_credentials'),
    //         ], 401);
    //     }

    //     // Single-session: revoke all previous tokens on fresh login
    //     $user->tokens()->delete();

    //     $role  = $user->getRoleNames()->first() ?? 'user';
    //     $token = $user->createToken("{$role}-token")->plainTextToken;

    //     $user->loadMissing(['pharmacy', 'branch']);

    //     return response()->json([
    //         'message'  => __('auth.messages.login_success'),
    //         'token'    => $token,
    //         'user'     => new UserResource($user),
    //         'redirect' => $this->redirectFor($user),
    //     ]);
    // }
    public function login(LoginRequest $request): JsonResponse
    {
        // 1. جلب البيانات الموثقة فقط
        $validated = $request->validated();

        // 2. التحقق من صحة البيانات واستخراج التوكن
        if (! $token = auth('api')->attempt($validated)) {
            return response()->json([
                'message' => __('auth.messages.invalid_credentials'),
            ], 401);
        }

        $user = auth('api')->user();

        // 5. تحميل العلاقات المطلوبة للـ Resource
        $user->loadMissing(['pharmacy', 'branch']);

        // (اختياري) إطلاق حدث تسجيل الدخول إذا كنت تستمع إليه لتحديث "آخر ظهور" مثلاً
        // event(new Login('sanctum', $user, false));

        return response()->json([
            'message'  => __('auth.messages.login_success'),
            'token'    => $token,
            'user'     => new UserResource($user),
            'redirect' => $this->redirectFor($user),
        ]);
    }
    /**
     * Self-registration for clients AND pharmacy owners.
     * POST /api/v1/auth/register
     *
     * Step 1 payload (both roles): name, email, password, password_confirmation, role
     * Step 2 payload (pharmacy_owner only):
     *   pharmacy_name_en, pharmacy_name_ar
     *   has_branches (boolean)
     *   city_id
     *   branch_name_en, branch_name_ar, branch_address_en, branch_address_ar, branch_phone (when has_branches = true)
     */
    public function register(RegisterClientRequest $request): JsonResponse
    {
        $role = $request->input('role', 'client');

        // ── Client registration (fast path) ──────────────────────────────
        if ($role === 'client') {
            $user = User::create([
                'name'        => $request->name,
                'email'       => $request->email,
                'password'    => Hash::make($request->password),
                'pharmacy_id' => null,
                'branch_id'   => null,
            ]);

            $user->assignRole('client');
            $token = auth('api')->login($user);

            return response()->json([
                'message'  => __('auth.messages.register_success'),
                'token'    => $token,
                'user'     => new UserResource($user),
                'redirect' => 'client.home',
            ], 201);
        }

        // ── Pharmacy Owner registration (with transaction) ────────────────
        DB::beginTransaction();

        try {
            // 1. Create user account (no pharmacy_id yet)
            $user = User::create([
                'name'        => $request->name,
                'email'       => $request->email,
                'password'    => Hash::make($request->password),
                'pharmacy_id' => null,
                'branch_id'   => null,
            ]);

            // 2. Create the pharmacy
            $pharmacy = Pharmacy::create([
                'pharmacy_name' => [
                    'en' => $request->pharmacy_name_en,
                    'ar' => $request->pharmacy_name_ar,
                ],
            ]);

            // 3. Link user to pharmacy
            $user->update(['pharmacy_id' => $pharmacy->id]);

            // 4. Create first branch
            $hasBranches = filter_var($request->input('has_branches'), FILTER_VALIDATE_BOOLEAN);

            if ($hasBranches) {
                // Owner specified branch details explicitly
                Branch::create([
                    'pharmacy_id'    => $pharmacy->id,
                    'city_id'        => (int) $request->city_id,
                    'branch_name'    => [
                        'en' => $request->branch_name_en,
                        'ar' => $request->branch_name_ar,
                    ],
                    'branch_address' => [
                        'en' => $request->branch_address_en,
                        'ar' => $request->branch_address_ar,
                    ],
                    'branch_phone'   => $request->branch_phone
                        ? ['en' => $request->branch_phone, 'ar' => $request->branch_phone]
                        : null,
                ]);
            } else {
                // Auto-create a main branch using the pharmacy name
                Branch::create([
                    'pharmacy_id'    => $pharmacy->id,
                    'city_id'        => (int) $request->city_id,
                    'branch_name'    => [
                        'en' => $request->pharmacy_name_en . ' - Main',
                        'ar' => $request->pharmacy_name_ar . ' - الرئيسي',
                    ],
                    'branch_address' => [
                        'en' => 'Main Branch',
                        'ar' => 'الفرع الرئيسي',
                    ],
                    'branch_phone'   => null,
                ]);
            }

            // 5. Assign pharmacy-owner role
            $user->assignRole('pharmacy-owner');

            DB::commit();

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Pharmacy Owner Registration Failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Registration failed. Please try again.',
            ], 500);
        }

        // 6. Issue JWT token after successful creation
        $user->loadMissing(['pharmacy', 'branch']);
        $token = auth('api')->login($user);

        return response()->json([
            'message'  => __('auth.messages.register_success'),
            'token'    => $token,
            'user'     => new UserResource($user),
            'redirect' => 'pharmacy.dashboard',
        ], 201);
    }
    // public function register(RegisterClientRequest $request): JsonResponse
    // {
    //     // 1. جلب البيانات الموثقة فقط
    //     $validated = $request->validated();

    //     // 2. استخدام DB Transaction لضمان سلامة البيانات مع Spatie
    //     DB::beginTransaction();

    //     try {
    //         $user = User::create([
    //             'name'        => $validated['name'],
    //             'email'       => $validated['email'],
    //             'password'    => Hash::make($validated['password']),
    //             'pharmacy_id' => null,
    //             'branch_id'   => null,
    //         ]);

    //         // تعيين الدور عبر حزمة Spatie
    //         $user->assignRole('client');

    //         DB::commit();
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         Log::error('User Registration Failed: ' . $e->getMessage());

    //         return response()->json([
    //             'message' => __('auth.messages.register_failed') // يفضل إضافة هذه الترجمة
    //         ], 500);
    //     }

    //     // 3. تأكيد التنبيهات / إرسال إيميل التفعيل التلقائي
    //     // سيقوم هذا الحدث بإرسال إشعار Verification إذا كان المودل يطبق MustVerifyEmail
    //     event(new Registered($user));

    //     // 4. إصدار التوكن
    //     $token = $user->createToken('client-token')->plainTextToken;

    //     return response()->json([
    //         'message'  => __('auth.messages.register_success'),
    //         'token'    => $token,
    //         'user'     => new UserResource($user),
    //         'redirect' => 'client.home',
    //     ], 201);
    // }
 //!--------------------------------
    // ──────────────────────────────────────────
    // Protected — requires auth:sanctum
    // ──────────────────────────────────────────

    /**
     * Logout current device.
     * POST /api/v1/auth/logout
     *
     * Revokes only the token used in this request.
     * Other devices remain logged in.
     */
    public function logout(Request $request): JsonResponse
    {
        auth('api')->logout();

        return response()->json([
            'message' => __('auth.messages.logout_success'),
        ]);
    }

    /**
     * Logout all devices.
     * POST /api/v1/auth/logout-all
     *
     * Revokes ALL tokens for this user.
     * Use after password change or security incident.
     */
    public function logoutAll(Request $request): JsonResponse
    {
        // For JWT, standard logout blacklists the current token.
        auth('api')->logout();

        return response()->json([
            'message' => __('auth.messages.logout_all_success'),
        ]);
    }

    /**
     * Rotate token.
     * POST /api/v1/auth/refresh
     *
     * Revokes the current token and issues a fresh one.
     * Call periodically to keep sessions alive securely.
     */
    public function refresh(Request $request): JsonResponse
    {
        $token = auth('api')->refresh();

        return response()->json([
            'message' => __('auth.messages.token_refreshed'),
            'token'   => $token,
        ]);
    }

    // ──────────────────────────────────────────
    // Private
    // ──────────────────────────────────────────

    /**
     * Map user role to a frontend route name.
     * Returned with every login response so the mobile app
     * knows which home screen to navigate to.
     */
    private function redirectFor(User $user): string
    {
        return match (true) {
            $user->isSuperAdmin()    => 'admin.dashboard',
            $user->isPharmacyOwner() => 'pharmacy.dashboard',
            $user->isBranchManager() => 'branch.dashboard',
            $user->isClient()        => 'client.home',
            default                  => 'home',
        };
    }
}
