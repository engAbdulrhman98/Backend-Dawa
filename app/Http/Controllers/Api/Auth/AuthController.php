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
 * المتحكم في المصادقة (AuthController)
 *
 * يدير عمليات التحقق من الهوية بالكامل:
 *   login      ← تسجيل الدخول الموحد لجميع الرتب والمستويات
 *   register   ← التسجيل الذاتي للعملاء وأصحاب الصيدليات
 *   logout     ← تسجيل الخروج للجهاز الحالي
 *   logout-all ← تسجيل الخروج لجميع الأجهزة النشطة
 *   refresh    ← تجديد التوكن لحفظ الجلسة
 */
class AuthController extends Controller
{
    // ──────────────────────────────────────────
    // مسارات عامة لا تحتاج لمصادقة
    // ──────────────────────────────────────────

    /**
     * تسجيل الدخول الموحد لكافة الرتب
     * POST /api/v1/auth/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // 1. استرجاع البيانات التي تم التحقق من صحتها من طلب الدخول
        $validated = $request->validated();

        // 2. التحقق من صحة بيانات الدخول وإصدار توكن JWT
        if (! $token = auth('api')->attempt($validated)) {
            return response()->json([
                'message' => __('auth.messages.invalid_credentials'), // رسالة خطأ مترجمة
            ], 401);
        }

        // 3. جلب بيانات المستخدم الموثق حالياً
        $user = auth('api')->user();

        // 4. تحميل الكيانات التابعة للمستخدم لخدمة الـ Resource
        $user->loadMissing(['pharmacy', 'branch']);

        // 5. إعادة الاستجابة بنجاح العملية مع التوكن والبيانات وواجهة التوجيه المناسبة لرتبته
        return response()->json([
            'message'  => __('auth.messages.login_success'),
            'token'    => $token,
            'user'     => new UserResource($user),
            'redirect' => $this->redirectFor($user),
        ]);
    }

    /**
     * التسجيل الموحد للعملاء أو أصحاب الصيدليات
     * POST /api/v1/auth/register
     */
    public function register(RegisterClientRequest $request): JsonResponse
    {
        $role = $request->input('role', 'client');

        // ── حالة تسجيل العميل (سريع ولا يتطلب بيانات صيدلية) ──────────────────────────────
        if ($role === 'client') {
            $user = User::create([
                'name'        => $request->name,
                'email'       => $request->email,
                'password'    => Hash::make($request->password), // تشفير كلمة المرور
                'pharmacy_id' => null,
                'branch_id'   => null,
            ]);

            $user->assignRole('client'); // إسناد صلاحية العميل عبر Spatie
            $token = auth('api')->login($user); // تسجيل الدخول الفوري وتوليد التوكن

            return response()->json([
                'message'  => __('auth.messages.register_success'),
                'token'    => $token,
                'user'     => new UserResource($user),
                'redirect' => 'client.home',
            ], 201);
        }

        // ── حالة تسجيل صاحب الصيدلية (يتطلب عمل معمليات متعددة متصلة) ────────────────
        DB::beginTransaction(); // بدء معاملة قاعدة البيانات لضمان عدم إنشاء حساب مشوه عند فشل أي خطوة

        try {
            // 1. إنشاء حساب المستخدم
            $user = User::create([
                'name'        => $request->name,
                'email'       => $request->email,
                'password'    => Hash::make($request->password),
                'pharmacy_id' => null,
                'branch_id'   => null,
            ]);

            // 2. إنشاء الصيدلية التابعة
            $pharmacy = Pharmacy::create([
                'pharmacy_name' => [
                    'en' => $request->pharmacy_name_en,
                    'ar' => $request->pharmacy_name_ar,
                ],
            ]);

            // 3. ربط حساب المستخدم بالصيدلية المنشأة
            $user->update(['pharmacy_id' => $pharmacy->id]);

            // 4. إنشاء الفرع الأول التلقائي أو المخصص للصيدلية
            $hasBranches = filter_var($request->input('has_branches'), FILTER_VALIDATE_BOOLEAN);

            if ($hasBranches) {
                // إذا حدد صاحب الصيدلية وجود فروع وقام بتعبئة بيانات الفرع
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
                // إنشاء فرع افتراضي تلقائياً باستخدام اسم الصيدلية الرئيسي لضمان وجود فرع
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

            // 5. إسناد دور مالك الصيدلية
            $user->assignRole('pharmacy-owner');

            DB::commit(); // اعتماد وإدخال البيانات في قاعدة البيانات بنجاح

        } catch (\Throwable $e) {
            DB::rollBack(); // التراجع عن العمليات السابقة لإلغاء البيانات المشوهة
            Log::error('فشل تسجيل صاحب صيدلية جديدة: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Registration failed. Please try again.',
            ], 500);
        }

        // 6. إصدار توكن JWT وتأكيد عملية التسجيل
        $user->loadMissing(['pharmacy', 'branch']);
        $token = auth('api')->login($user);

        return response()->json([
            'message'  => __('auth.messages.register_success'),
            'token'    => $token,
            'user'     => new UserResource($user),
            'redirect' => 'pharmacy.dashboard',
        ], 201);
    }

    // ──────────────────────────────────────────
    // مسارات محمية تحتاج إلى تسجيل دخول وتمرير التوكن
    // ──────────────────────────────────────────

    /**
     * تسجيل خروج من الجهاز الحالي
     * POST /api/v1/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        auth('api')->logout(); // إبطال التوكن الحالي المستخدم في الطلب

        return response()->json([
            'message' => __('auth.messages.logout_success'),
        ]);
    }

    /**
     * تسجيل خروج من كافة الأجهزة
     * POST /api/v1/auth/logout-all
     */
    public function logoutAll(Request $request): JsonResponse
    {
        // لتسجيل الخروج الكلي في JWT، نقوم بإبطال التوكن الحالي
        auth('api')->logout();

        return response()->json([
            'message' => __('auth.messages.logout_all_success'),
        ]);
    }

    /**
     * تجديد توكن JWT (Token Rotation)
     * POST /api/v1/auth/refresh
     */
    public function refresh(Request $request): JsonResponse
    {
        $token = auth('api')->refresh(); // توليد توكن جديد وإبطال القديم لحفظ أمان الجلسة

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
