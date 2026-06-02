<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\GovernorateController;
use App\Http\Controllers\Api\MedicineController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PharmacyController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\MedicineExcelController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| مسارات الـ API (API Routes)
|--------------------------------------------------------------------------
| هنا يتم تعريف مسارات تطبيق الويب. كل المسارات مدرجة تحت البادئة v1
| وتمر عبر وسيط set_locale لضبط لغة الاستجابة.
*/

Route::prefix('v1')->middleware(['set_locale'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 1. المسارات العامة (لا تتطلب تسجيل دخول)
    |--------------------------------------------------------------------------
    */
    
    // مسارات المصادقة والتسجيل (Authentication Routes)
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']); // إنشاء حساب جديد
        // تسجيل الدخول مع محدد معدل الطلبات (Throttle) بـ 5 محاولات كحد أقصى في الدقيقة لمنع الاختراق
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1'); 
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']); // نسيان كلمة المرور
        Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset'); // إعادة تعيين كلمة المرور
    });

    // مسارات التصفح العام لبيانات الموقع الجغرافي (الدول والمحافظات والمدن)
    Route::get('countries', [CountryController::class, 'index']);        // جلب جميع الدول
    Route::get('countries/{country}', [CountryController::class, 'show']); // جلب تفاصيل دولة محددة

    Route::get('governorates', [GovernorateController::class, 'indexAll']); // قائمة بجميع المحافظات
    Route::get('countries/{country}/governorates', [GovernorateController::class, 'index']); // محافظات تابعة لدولة معينة
    Route::get('governorates/{governorate}', [GovernorateController::class, 'show']); // تفاصيل محافظة محددة

    Route::get('cities', [CityController::class, 'indexAll']); // قائمة بجميع المدن
    Route::get('governorates/{governorate}/cities', [CityController::class, 'index']); // مدن تابعة لمحافظة معينة
    Route::get('cities/{city}', [CityController::class, 'show']); // تفاصيل مدينة محددة

    // مسارات تصفح الأقسام والتصنيفات للأدوية
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index'); // قائمة الأقسام
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show'); // تفاصيل قسم محدد (عبر slug)

    // مسارات البحث وتصفح الأدوية
    Route::get('medicines/search', [MedicineController::class, 'search']); // البحث عن الأدوية
    Route::get('medicines', [MedicineController::class, 'index']); // قائمة جميع الأدوية بنظام الصفحات (Pagination)
    Route::get('medicines/{medicine}', [MedicineController::class, 'show']); // تفاصيل دواء محدد
    Route::get('categories/{category}/medicines', [MedicineController::class, 'indexByCategory'])->name('categories.medicines.index'); // جلب أدوية قسم معين

    // مسارات البحث واكتشاف الصيدليات
    Route::get('pharmacies/search', [PharmacyController::class, 'search']); // البحث عن صيدلية بالاسم أو الموقع
    Route::get('pharmacies', [PharmacyController::class, 'index']);        // قائمة الصيدليات
    Route::get('pharmacies/{pharmacy}', [PharmacyController::class, 'show']); // تفاصيل صيدلية محددة

    // مسارات تصفح الفروع
    Route::get('branches/nearby', [BranchController::class, 'nearby']);    // البحث الجغرافي عن الفروع القريبة
    Route::get('branches', [BranchController::class, 'index']);            // قائمة الفروع
    Route::get('branches/{branch}', [BranchController::class, 'show']);    // تفاصيل فرع محدد
    Route::get('pharmacies/{pharmacy}/branches', [BranchController::class, 'indexByPharmacy']); // فروع تابعة لصيدلية معينة

    /*
    |--------------------------------------------------------------------------
    | 2. المسارات المحمية (تتطلب تسجيل دخول وتوفر توكن JWT)
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:api')->group(function () {

        // معلومات الجلسة والملف الشخصي للمستخدم الحالي
        Route::post('auth/logout', [AuthController::class, 'logout']); // تسجيل الخروج للجلسة الحالية
        Route::post('auth/logout-all', [AuthController::class, 'logoutAll']); // تسجيل الخروج من جميع الأجهزة
        Route::get('me', [UserController::class, 'me']); // جلب بيانات المستخدم الحالي
        Route::match(['put', 'patch'], 'me', [UserController::class, 'updateMe']); // تحديث الملف الشخصي

        // إحصائيات لوحة التحكم للمالك أو الأدمن
        Route::get('dashboard/stats', [DashboardController::class, 'stats']);

        // إدارة الإشعارات الخاصة بالملف الشخصي
        Route::prefix('me/notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index']); // قائمة الإشعارات
            Route::post('read-all', [NotificationController::class, 'markAllAsRead']); // تحديد الكل كمقروء
            Route::post('{id}/read', [NotificationController::class, 'markAsRead']); // تحديد إشعار كمقروء
            Route::delete('/', [NotificationController::class, 'destroyAll']); // حذف كافة الإشعارات
            Route::get('{id}', [NotificationController::class, 'show']); // عرض إشعار محدد
            Route::delete('{id}', [NotificationController::class, 'destroy']); // حذف إشعار محدد
        });

        // ميزة خاصة بالعميل: طلب فحص الصيدليات القريبة وإرسال إشعار
        Route::post('me/nearby-pharmacies', [NotificationController::class, 'nearbyPharmacies'])
            ->middleware('role:client|super-admin');

        /*
        |--------------------------------------------------------------------------
        | 3. مسارات أصحاب الصيدليات ومدير النظام (Pharmacy Owner + Super Admin)
        |--------------------------------------------------------------------------
        */
        Route::middleware(['role:pharmacy-owner|super-admin'])->group(function () {

            // إدارة بيانات الأدوية (إضافة، تعديل، حذف) عبر الـ CRUD
            Route::apiResource('categories.medicines', MedicineController::class)
                ->shallow()
                ->except(['index', 'show']);

            // إدارة الفروع (إضافة، تعديل، حذف)
            Route::apiResource('branches', BranchController::class)->except(['index', 'show']);

            // تنبيهات المخزون المتدني والانتهاء القريب للصلاحية للأدوية في الفروع
            Route::get('branches/{branch}/low-stock', [BranchController::class, 'lowStock']);
            Route::get('branches/{branch}/expiring-soon', [BranchController::class, 'expiringSoon']);

            // تحديث تفاصيل الصيدلية الخاصة بالمالك
            Route::match(['put', 'patch'], 'pharmacies/{pharmacy}', [PharmacyController::class, 'update']);
        });

        /*
        |--------------------------------------------------------------------------
        | 4. مسارات مدير النظام فقط (Super Admin Only) - إدارة البنية التحتية
        |--------------------------------------------------------------------------
        */
        Route::middleware(['role:super-admin'])->group(function () {

            // إدارة المستخدمين والصيدليات والأقسام بالكامل
            Route::apiResource('users', UserController::class);
            Route::apiResource('pharmacies', PharmacyController::class)->only(['store', 'destroy']);
            Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);

            // استيراد وتصدير بيانات الأدوية عبر إكسل (Excel Import/Export for Medicines)
            Route::post('medicines/import', [MedicineExcelController::class, 'import']);
            Route::get('medicines/export', [MedicineExcelController::class, 'export']);

            // إدارة الدول (إضافة وتعديل وحذف)
            Route::post('countries', [CountryController::class, 'store']);
            Route::put('countries/{country}', [CountryController::class, 'update']);
            Route::delete('countries/{country}', [CountryController::class, 'destroy']);

            // إدارة المحافظات (إضافة وتعديل وحذف)
            Route::post('countries/{country}/governorates', [GovernorateController::class, 'store']);
            Route::put('governorates/{governorate}', [GovernorateController::class, 'update']);
            Route::delete('governorates/{governorate}', [GovernorateController::class, 'destroy']);

            // إدارة المدن (إضافة وتعديل وحذف)
            Route::post('governorates/{governorate}/cities', [CityController::class, 'store']);
            Route::put('cities/{city}', [CityController::class, 'update']);
            Route::delete('cities/{city}', [CityController::class, 'destroy']);
        });
    });
});

