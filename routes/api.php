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
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['set_locale'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | 1. PUBLIC ROUTES (No Auth Required)
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']); //* done
        //Route::post('login', [AuthController::class, 'login']);
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1'); //* done
        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');

        // Social Auth
        // Route::get('google', [AuthController::class, 'redirectToGoogle']);
        // Route::get('google/callback', [AuthController::class, 'handleGoogleCallback']);
    });

    // Public Browsing (Read-Only)
    Route::get('countries', [CountryController::class, 'index']);        // GET all countries  //* done
    Route::get('countries/{country}', [CountryController::class, 'show']); // GET single country //* done


    Route::get('governorates', [GovernorateController::class, 'indexAll']); // List all //* done
    Route::get('countries/{country}/governorates', [GovernorateController::class, 'index']); //* done
    Route::get('governorates/{governorate}', [GovernorateController::class, 'show']); //* done

    Route::get('cities', [CityController::class, 'indexAll']); // List all //* done
    Route::get('governorates/{governorate}/cities', [CityController::class, 'index']); //* done
    Route::get('cities/{city}', [CityController::class, 'show']); //* done

    // GET /api/v1/categories -> List all categories
    Route::get('categories', [CategoryController::class, 'index']) //* done
        ->name('categories.index');
    // GET /api/v1/categories/{category} -> View specific category details
    // Note: {category} should be the category_slug (e.g., 'analgesics')
    Route::get('categories/{category}', [CategoryController::class, 'show']) //* done
        ->name('categories.show');

    Route::get('medicines/search', [MedicineController::class, 'search']); //* done
    //Route::get('medicines', [MedicineController::class, 'indexAll']);
    Route::get('medicines', [MedicineController::class, 'index']); //* done
    Route::get('medicines/{medicine}', [MedicineController::class, 'show']); //* done
    // Filter medicines BY CATEGORY (Nested Index)
    // GET /api/v1/categories/{category}/medicines
    Route::get('categories/{category}/medicines', [MedicineController::class, 'indexByCategory']) //* done
        ->name('categories.medicines.index');

    // Pharmacy Discovery
    Route::get('pharmacies/search', [PharmacyController::class, 'search']); // Search by name/location //* done
    Route::get('pharmacies', [PharmacyController::class, 'index']);        // List all pharmacies //* done
    Route::get('pharmacies/{pharmacy}', [PharmacyController::class, 'show']); // View one pharmacy //* done

    // Branch Discovery
    Route::get('branches/nearby', [BranchController::class, 'nearby']);    // Geo-location search
    Route::get('branches', [BranchController::class, 'index']);            // List all branches //* done
    Route::get('branches/{branch}', [BranchController::class, 'show']);    // View one branch details //* done

    // Relationship
    Route::get('pharmacies/{pharmacy}/branches', [BranchController::class, 'indexByPharmacy']); //* done

    /*
    |--------------------------------------------------------------------------
    | 2. PROTECTED ROUTES (Requires JWT Auth)
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:api')->group(function () {

        // Session & Profile
        Route::post('auth/logout', [AuthController::class, 'logout']); //* done
        Route::post('auth/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('me', [UserController::class, 'me']); //* done
        Route::match(['put', 'patch'], 'me', [UserController::class, 'updateMe']);

        // Dashboard Stats
        Route::get('dashboard/stats', [DashboardController::class, 'stats']);

        // Notifications (Self-service)
        Route::prefix('me/notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::post('read-all', [NotificationController::class, 'markAllAsRead']);
            Route::post('{id}/read', [NotificationController::class, 'markAsRead']);
            Route::delete('/', [NotificationController::class, 'destroyAll']);
            Route::get('{id}', [NotificationController::class, 'show']);
            Route::delete('{id}', [NotificationController::class, 'destroy']);
        });

        // Client Feature: Request nearby pharmacies check
        Route::post('me/nearby-pharmacies', [NotificationController::class, 'nearbyPharmacies'])
            ->middleware('role:client|super-admin');

        /*
        |--------------------------------------------------------------------------
        | 3. PHARMACY OWNER + SUPER ADMIN
        |--------------------------------------------------------------------------
        */
        Route::middleware(['role:pharmacy-owner|super-admin'])->group(function () {

            // Medicine Management (Shallow)
            Route::apiResource('categories.medicines', MedicineController::class)
                ->shallow()
                ->except(['index', 'show']);

            // Branch Management
            Route::apiResource('branches', BranchController::class)->except(['index', 'show']);

            // Stock Alerts
            Route::get('branches/{branch}/low-stock', [BranchController::class, 'lowStock']);
            Route::get('branches/{branch}/expiring-soon', [BranchController::class, 'expiringSoon']);

            // Update own pharmacy details
            Route::match(['put', 'patch'], 'pharmacies/{pharmacy}', [PharmacyController::class, 'update']);
        });

        /*
        |--------------------------------------------------------------------------
        | 4. SUPER ADMIN ONLY (Infrastructure)
        |--------------------------------------------------------------------------
        */
        Route::middleware(['role:super-admin'])->group(function () {

            // User & Pharmacy Management
            Route::apiResource('users', UserController::class);
            Route::apiResource('pharmacies', PharmacyController::class)->only(['store', 'destroy']);
            Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);

            // Geography Management (Full CRUD) — super-admin only

            // Countries: POST /countries, PUT /countries/{c}, DELETE /countries/{c}
            Route::post('countries', [CountryController::class, 'store']);
            Route::put('countries/{country}', [CountryController::class, 'update']);
            Route::delete('countries/{country}', [CountryController::class, 'destroy']);

            // Governorates: nested POST, shallow PUT + DELETE
            Route::post('countries/{country}/governorates', [GovernorateController::class, 'store']);
            Route::put('governorates/{governorate}', [GovernorateController::class, 'update']);
            Route::delete('governorates/{governorate}', [GovernorateController::class, 'destroy']);

            // Cities: nested POST, shallow PUT + DELETE
            Route::post('governorates/{governorate}/cities', [CityController::class, 'store']);
            Route::put('cities/{city}', [CityController::class, 'update']);
            Route::delete('cities/{city}', [CityController::class, 'destroy']);
        });
    });
});
