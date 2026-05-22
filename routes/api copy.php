<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    Auth\AuthController,
    CountryController,
    GovernorateController,
    CityController,
    CategoryController,
    MedicineController,
    PharmacyController,
    BranchController,
    NotificationController,
    UserController
};

Route::prefix('v1')->middleware(['set_locale'])->group(function () {

    /*
    |--------------------------------------------------
    | AUTH (PUBLIC)
    |--------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
    });

    /*
    |--------------------------------------------------
    | LOCATION (Nested + Shallow)
    |--------------------------------------------------
    */

    // Countries (public read)
    Route::apiResource('countries', CountryController::class)
        ->only(['index', 'show']);

    // Governorates (nested under country + shallow)
    Route::apiResource('countries.governorates', GovernorateController::class)
        ->shallow()
        ->only(['index', 'show']);

    // All governorates (non-nested)
    Route::get('governorates', [GovernorateController::class, 'indexAll']);

    // Cities (nested under governorate + shallow)
    Route::apiResource('governorates.cities', CityController::class)
        ->shallow()
        ->only(['index', 'show']);

    // All cities (non-nested)
    Route::get('cities', [CityController::class, 'indexAll']);

    /*
    |--------------------------------------------------
    | CATEGORIES & MEDICINES
    |--------------------------------------------------
    */

    Route::apiResource('categories', CategoryController::class)
        ->only(['index', 'show']);

    Route::get('medicines', [MedicineController::class, 'index']);
    Route::get('medicines/search', [MedicineController::class, 'search']); // BEFORE {medicine}
    Route::get('medicines/{medicine}', [MedicineController::class, 'show']);

    // Nested
    Route::get('categories/{category}/medicines', [MedicineController::class, 'indexByCategory']);

    /*
    |--------------------------------------------------
    | PHARMACIES
    |--------------------------------------------------
    */

    Route::get('pharmacies', [PharmacyController::class, 'index']);
    Route::get('pharmacies/search', [PharmacyController::class, 'search']);
    Route::get('pharmacies/{pharmacy}', [PharmacyController::class, 'show']);

    // Nested
    Route::get('pharmacies/{pharmacy}/medicines', [PharmacyController::class, 'medicines']);
    Route::get('pharmacies/{pharmacy}/branches', [BranchController::class, 'indexByPharmacy']);

    Route::get('pharmacies/{pharmacy}/nearest-branch', [PharmacyController::class, 'nearestBranch']);

    /*
    |--------------------------------------------------
    | BRANCHES
    |--------------------------------------------------
    */

    Route::get('branches', [BranchController::class, 'index']);
    Route::get('branches/search', [BranchController::class, 'search']); // BEFORE {branch}
    Route::get('branches/nearby', [BranchController::class, 'nearby']); // BEFORE {branch}
    Route::get('branches/{branch}', [BranchController::class, 'show']);

    /*
    |--------------------------------------------------
    | AUTHENTICATED USERS
    |--------------------------------------------------
    */
    Route::middleware(['auth:sanctum'])->group(function () {

        /*
        |-------------------------
        | AUTH
        |-------------------------
        */
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);

        /*
        |-------------------------
        | PROFILE
        |-------------------------
        */
        Route::get('me', [UserController::class, 'me']);
        Route::match(['put', 'patch'], 'me', [UserController::class, 'update']);

        /*
        |-------------------------
        | NOTIFICATIONS (ALL ROLES)
        |-------------------------
        */
        Route::prefix('me/notifications')->group(function () {
            Route::get('/', [NotificationController::class, 'index']);
            Route::get('{id}', [NotificationController::class, 'show']);
            Route::post('read-all', [NotificationController::class, 'markAllAsRead']);
            Route::post('{id}/read', [NotificationController::class, 'markAsRead']);
            Route::delete('/', [NotificationController::class, 'destroyAll']);
            Route::delete('{id}', [NotificationController::class, 'destroy']);
        });

        /*
        |--------------------------------------------------
        | SUPER ADMIN
        |--------------------------------------------------
        */
        Route::middleware(['role:super-admin'])->group(function () {

            // Full control over locations
            Route::apiResource('countries', CountryController::class)->except(['index', 'show']);
            Route::apiResource('countries.governorates', GovernorateController::class)
                ->shallow()
                ->except(['index', 'show']);
            Route::apiResource('governorates.cities', CityController::class)
                ->shallow()
                ->except(['index', 'show']);

            // Categories & medicines
            Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);

            Route::post('medicines', [MedicineController::class, 'store']);
            Route::match(['put', 'patch'], 'medicines/{medicine}', [MedicineController::class, 'update']);
            Route::delete('medicines/{medicine}', [MedicineController::class, 'destroy']);

            // Pharmacies
            Route::post('pharmacies', [PharmacyController::class, 'store']);
            Route::delete('pharmacies/{pharmacy}', [PharmacyController::class, 'destroy']);

            // Users
            Route::get('users', [UserController::class, 'index']);
            Route::delete('users/{user}', [UserController::class, 'destroy']);
        });

        /*
        |--------------------------------------------------
        | PHARMACY OWNER
        |--------------------------------------------------
        */
        Route::middleware(['role:pharmacy-owner'])->group(function () {

            Route::post('pharmacies', [PharmacyController::class, 'store']);
            Route::match(['put', 'patch'], 'pharmacies/{pharmacy}', [PharmacyController::class, 'update']);

            Route::post('branches', [BranchController::class, 'store']);
            Route::match(['put', 'patch'], 'branches/{branch}', [BranchController::class, 'update']);

            Route::post('users', [UserController::class, 'store']); // create managers
        });

        /*
        |--------------------------------------------------
        | BRANCH MANAGER
        |--------------------------------------------------
        */
        Route::middleware(['role:branch-manager'])->group(function () {

            Route::get('branches/{branch}/low-stock', [BranchController::class, 'lowStock']);
            Route::get('branches/{branch}/expiring-soon', [BranchController::class, 'expiringSoon']);
        });

        /*
        |--------------------------------------------------
        | CLIENT
        |--------------------------------------------------
        */
        Route::middleware(['role:client'])->group(function () {

            Route::post('me/nearby-pharmacies', [NotificationController::class, 'nearbyPharmacies']);
        });
    });
});
