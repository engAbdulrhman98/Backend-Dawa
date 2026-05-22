<?php

// use App\Http\Controllers\Api\Auth\AuthController;
// use App\Http\Controllers\Api\BranchController;
// use App\Http\Controllers\Api\CategoryController;
// use App\Http\Controllers\Api\CityController;
// use App\Http\Controllers\Api\CountryController;
// use App\Http\Controllers\Api\GovernorateController;
// use App\Http\Controllers\Api\MedicineController;
// use App\Http\Controllers\Api\NotificationController;
// use App\Http\Controllers\Api\PharmacyController;
// use App\Http\Controllers\Api\UserController;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


// /*
// |--------------------------------------------------------------------------
// | Public Routes
// |--------------------------------------------------------------------------
// */
// // --- Public Routes ---
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
// Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
// // --- Google Auth Routes ---
// Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
// Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// /*
// |--------------------------------------------------------------------------
// | Protected Routes (Requires Bearer Token)
// |--------------------------------------------------------------------------
// */
// // --- Protected Routes ---
// Route::middleware('auth:sanctum')->group(function () {
//     Route::get('/user', [AuthController::class, 'user']);
//     Route::post('/logout', [AuthController::class, 'logout']);
// });



// Route::middleware(['set_locale'])
//     ->prefix('v1')
//     ->name('api.')
//     ->group(function () {

//         // ---------------------------------------------------------------
//         // COUNTRIES
//         // GET    /api/v1/countries
//         // POST   /api/v1/countries
//         // GET    /api/v1/countries/{country}
//         // PUT    /api/v1/countries/{country}
//         // DELETE /api/v1/countries/{country}
//         // ---------------------------------------------------------------
//         Route::apiResource('countries', CountryController::class)
//             ->parameters(['countries' => 'country']);

//         // ---------------------------------------------------------------
//         // GOVERNORATES — shallow nested (store always needs a country)
//         //
//         // NESTED:
//         //   GET    /api/v1/countries/{country}/governorates   → index
//         //   POST   /api/v1/countries/{country}/governorates   → store
//         //
//         // NON-NESTED (shallow):
//         //   GET    /api/v1/governorates/{governorate}         → show
//         //   PUT    /api/v1/governorates/{governorate}         → update
//         //   DELETE /api/v1/governorates/{governorate}         → destroy
//         //
//         // EXTRA NON-NESTED:
//         //   GET    /api/v1/governorates                       → index all
//         // ---------------------------------------------------------------
//         Route::apiResource('countries.governorates', GovernorateController::class)
//             ->shallow()
//             ->parameters([
//                 'countries' => 'country',
//                 'governorates' => 'governorate',
//             ]);

//         // Standalone index — list ALL governorates across all countries
//         // ?filter[governorate_name]=Cairo
//         // ?filter[country_id]=1
//         // ?sort=governorate_slug
//         Route::get('governorates', [GovernorateController::class, 'indexAll'])
//             ->name('governorates.indexAll');

//         // ---------------------------------------------------------------
//         // CITIES — shallow nested (store always needs a governorate)
//         //
//         // NESTED:
//         //   GET    /api/v1/governorates/{governorate}/cities  → index
//         //   POST   /api/v1/governorates/{governorate}/cities  → store
//         //
//         // NON-NESTED (shallow):
//         //   GET    /api/v1/cities/{city}                      → show
//         //   PUT    /api/v1/cities/{city}                      → update
//         //   DELETE /api/v1/cities/{city}                      → destroy
//         //
//         // EXTRA NON-NESTED:
//         //   GET    /api/v1/cities                             → index all
//         // ---------------------------------------------------------------
//         Route::apiResource('governorates.cities', CityController::class)
//             ->shallow()
//             ->parameters([
//                 'governorates' => 'governorate',
//                 'cities' => 'city',
//             ]);
//         // Standalone index — list ALL cities across all governorates
//         // ?filter[city_name]=Nasr
//         // ?filter[governorate_id]=1
//         // ?sort=city_slug
//         Route::get('cities', [CityController::class, 'indexAll'])
//             ->name('cities.indexAll');

//         Route::apiResource('categories', CategoryController::class)
//             ->parameters(['categories' => 'category']);
//         // // All medicines across all categories
//         Route::get('medicines', [MedicineController::class, 'indexAll'])
//             ->name('medicines.indexAll');
//         // Route::apiResource('categories.medicines', MedicineController::class)
//         //     ->parameters(['medicines' => 'medicine'])
//         //     ->shallow();

//         // ── Public routes ────────────────────────────────────────────────────

//         // GET /api/v1/medicines
//         // Route::get('medicines', [MedicineController::class, 'index'])
//         //     ->name('medicines.index');

//         // GET /api/v1/medicines/search?q=para&locale=en
//         // Must be defined BEFORE {medicine} to avoid slug conflict
//         Route::get('medicines/search', [MedicineController::class, 'search'])
//             ->name('medicines.search');


//         // GET /api/v1/categories/{category}/medicines
//         Route::get('categories/{category}/medicines', [MedicineController::class, 'indexByCategory'])
//             ->name('categories.medicines.index');

//         // ── Protected routes (super-admin only) ──────────────────────────────
//         Route::middleware(['auth:sanctum'])->group(function () {

//             // POST /api/v1/medicines
//             Route::post('medicines', [MedicineController::class, 'store'])
//                 ->name('medicines.store');
//         });
//     });

// Route::apiResource('categories', CategoryController::class)
//     ->parameters(['categories' => 'category']);
// // All medicines across all categories
// Route::get('medicines', [MedicineController::class, 'indexAll'])
//     ->name('medicines.indexAll');
// Route::apiResource('categories.medicines', MedicineController::class)
//     ->parameters(['medicines' => 'medicine'])
//     ->shallow();

// //     ### Query string examples
// // ```
// // GET /api/medicines?filter[name]=paracetamol&include=category
// // GET /api/medicines?filter[category_slug]=antibiotics&include=category,branches
// // GET /api/medicines?filter[in_stock]=1&filter[expiring_soon]=30
// // GET /api/medicines?filter[category]=2&sort=-created_at&per_page=10

// /*
// |--------------------------------------------------------------------------
// | Branch API Routes
// |--------------------------------------------------------------------------
// |
// | Base URL: /api
// |
// | Public routes   → no auth required (clients browsing)
// | Protected routes → requires sanctum auth + role check in FormRequest
// |
// */

// // ── Public routes (clients can browse without login) ──────────────────────
// Route::prefix('v1')->group(function () {

//     // GET /api/v1/branches
//     // GET /api/v1/branches?filter[in_city]=3&include=pharmacy,city
//     Route::get('branches', [BranchController::class, 'index'])
//         ->name('branches.index');

//     // GET /api/v1/branches/nearby?lat=30.0444&lng=31.2357&radius=5
//     // Must be defined BEFORE {branch} route to avoid slug conflict
//     Route::get('branches/nearby', [BranchController::class, 'nearby'])
//         ->name('branches.nearby');

//     // GET /api/v1/branches/{branch}  (branch = branch_slug)
//     Route::get('branches/{branch}', [BranchController::class, 'show'])
//         ->name('branches.show');

//     // GET /api/v1/pharmacies/{pharmacy}/branches
//     Route::get('pharmacies/{pharmacy}/branches', [BranchController::class, 'indexByPharmacy'])
//         ->name('pharmacies.branches.index');
// });

// // ── Protected routes (pharmacy owners + super admin only) ─────────────────
// Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {

//     // POST /api/v1/branches
//     Route::post('branches', [BranchController::class, 'store'])
//         ->name('branches.store');

//     // PUT  /api/v1/branches/{branch}
//     // PATCH /api/v1/branches/{branch}
//     Route::match(['put', 'patch'], 'branches/{branch}', [BranchController::class, 'update'])
//         ->name('branches.update');

//     // DELETE /api/v1/branches/{branch}
//     Route::delete('branches/{branch}', [BranchController::class, 'destroy'])
//         ->name('branches.destroy');
// });

// Route::prefix('v2')->middleware('set_locale')->group(function () {

//     // ── Public routes ────────────────────────────────────────────────────

//     // GET /api/v1/medicines
//     Route::get('medicines', [MedicineController::class, 'index'])
//         ->name('medicines.index');

//     // GET /api/v1/medicines/search?q=para&locale=en
//     // Must be defined BEFORE {medicine} to avoid slug conflict
//     Route::get('medicines/search', [MedicineController::class, 'search'])
//         ->name('medicines.search');

//     // GET /api/v1/medicines/{medicine}  (medicine = medicine_slug)
//     Route::get('medicines/{medicine}', [MedicineController::class, 'show'])
//         ->name('medicines.show');

//     // GET /api/v1/categories/{category}/medicines
//     Route::get('categories/{category}/medicines', [MedicineController::class, 'indexByCategory'])
//         ->name('categories.medicines.index');

//     // ── Protected routes (super-admin only) ──────────────────────────────
//     //* Route::middleware(['auth:sanctum'])->group(function () {

//     // POST /api/v1/medicines
//     Route::post('medicines', [MedicineController::class, 'store'])
//         ->name('medicines.store');

//     // PUT  /api/v1/medicines/{medicine}
//     // PATCH /api/v1/medicines/{medicine}
//     Route::match(['put', 'patch'], 'medicines/{medicine}', [MedicineController::class, 'update'])
//         ->name('medicines.update');

//     // DELETE /api/v1/medicines/{medicine}
//     Route::delete('medicines/{medicine}', [MedicineController::class, 'destroy'])
//         ->name('medicines.destroy');
//     //* });
// });

// Route::prefix('v1')->group(function () {

//     // ── Public routes ─────────────────────────────────────────────────────

//     // GET /api/v1/pharmacies
//     // GET /api/v1/pharmacies?filter[in_city]=3&include=branches
//     Route::get('pharmacies', [PharmacyController::class, 'index'])
//         ->name('pharmacies.index');

//     // GET /api/v1/pharmacies/search?q=cairo&locale=en
//     // GET /api/v1/pharmacies/search?q=cairo&city_id=3&medicine=5&nearby=30.04,31.23,5
//     // Must be defined BEFORE {pharmacy} to avoid 'search' being matched as a slug
//     Route::get('pharmacies/search', [PharmacyController::class, 'search'])
//         ->name('pharmacies.search');

//     // GET /api/v1/pharmacies/{pharmacy}  (pharmacy = pharmacy_slug)
//     Route::get('pharmacies/{pharmacy}', [PharmacyController::class, 'show'])
//         ->name('pharmacies.show');

//     // GET /api/v1/pharmacies/{pharmacy}/medicines
//     // GET /api/v1/pharmacies/{pharmacy}/medicines?filter[in_stock]=1
//     Route::get('pharmacies/{pharmacy}/medicines', [PharmacyController::class, 'medicines'])
//         ->name('pharmacies.medicines');

//     // GET /api/v1/pharmacies/{pharmacy}/nearest-branch?lat=30.04&lng=31.23
//     Route::get('pharmacies/{pharmacy}/nearest-branch', [PharmacyController::class, 'nearestBranch'])
//         ->name('pharmacies.nearest-branch');

//     // ── Protected routes ──────────────────────────────────────────────────
//     //* Route::middleware(['auth:sanctum'])->group(function () {

//     // POST /api/v1/pharmacies
//     // Auth: super-admin only
//     Route::post('pharmacies', [PharmacyController::class, 'store'])
//         ->name('pharmacies.store');

//     // PUT  /api/v1/pharmacies/{pharmacy}
//     // PATCH /api/v1/pharmacies/{pharmacy}
//     // Auth: super-admin or pharmacy-owner (own pharmacy)
//     Route::match(['put', 'patch'], 'pharmacies/{pharmacy}', [PharmacyController::class, 'update'])
//         ->name('pharmacies.update');

//     // DELETE /api/v1/pharmacies/{pharmacy}
//     // Auth: super-admin only
//     Route::delete('pharmacies/{pharmacy}', [PharmacyController::class, 'destroy'])
//         ->name('pharmacies.destroy');

//     // GET /api/v1/pharmacies/{pharmacy}/low-stock?threshold=10
//     // Auth: super-admin or pharmacy-owner (own pharmacy)
//     Route::get('pharmacies/{pharmacy}/low-stock', [PharmacyController::class, 'lowStock'])
//         ->name('pharmacies.low-stock');

//     // GET /api/v1/pharmacies/{pharmacy}/expiring-soon?days=30
//     // Auth: super-admin or pharmacy-owner (own pharmacy)
//     Route::get('pharmacies/{pharmacy}/expiring-soon', [PharmacyController::class, 'expiringSoon'])
//         ->name('pharmacies.expiring-soon');
//     //*** */ });

// });


// /*
// |--------------------------------------------------------------------------
// | Branch API Routes
// |--------------------------------------------------------------------------
// |
// | Base URL: /api/v1
// |
// | IMPORTANT: 'search' and 'nearby' MUST be defined
// | BEFORE {branch} to avoid slug conflicts.
// |
// */

// Route::prefix('v1')->group(function () {

//     // ── Public routes ─────────────────────────────────────────────────────

//     // GET /api/v1/branches
//     Route::get('branches', [BranchController::class, 'index'])
//         ->name('branches.index');

//     // GET /api/v1/branches/search?q=cairo&locale=en
//     // ⚠️ Must be BEFORE {branch}
//     Route::get('branches/search', [BranchController::class, 'search'])
//         ->name('branches.search');

//     // GET /api/v1/branches/nearby?lat=30.0444&lng=31.2357&radius=5
//     // ⚠️ Must be BEFORE {branch}
//     Route::get('branches/nearby', [BranchController::class, 'nearby'])
//         ->name('branches.nearby');

//     // GET /api/v1/branches/{branch}  (branch = branch_slug)
//     Route::get('branches/{branch}', [BranchController::class, 'show'])
//         ->name('branches.show');

//     // GET /api/v1/pharmacies/{pharmacy}/branches
//     Route::get('pharmacies/{pharmacy}/branches', [BranchController::class, 'indexByPharmacy'])
//         ->name('pharmacies.branches.index');

//     // ── Protected routes ──────────────────────────────────────────────────
//     //** */  Route::middleware(['auth:sanctum'])->group(function () {

//     // POST   /api/v1/branches
//     Route::post('branches', [BranchController::class, 'store'])
//         ->name('branches.store');

//     // PUT|PATCH /api/v1/branches/{branch}
//     Route::match(['put', 'patch'], 'branches/{branch}', [BranchController::class, 'update'])
//         ->name('branches.update');

//     // DELETE /api/v1/branches/{branch}
//     Route::delete('branches/{branch}', [BranchController::class, 'destroy'])
//         ->name('branches.destroy');

//     // GET /api/v1/branches/{branch}/low-stock?threshold=10
//     Route::get('branches/{branch}/low-stock', [BranchController::class, 'lowStock'])
//         ->name('branches.low-stock');

//     // GET /api/v1/branches/{branch}/expiring-soon?days=30
//     Route::get('branches/{branch}/expiring-soon', [BranchController::class, 'expiringSoon'])
//         ->name('branches.expiring-soon');
//     //** */ });
// });

// /*
// |--------------------------------------------------------------------------
// | Notification API Routes
// |--------------------------------------------------------------------------
// | Base URL: /api/v1
// | All routes require auth:sanctum
// |
// | Notification types:
// |   low_stock          → sent to branch-manager + pharmacy-owner
// |   nearby_pharmacies  → sent to client
// */

// Route::prefix('v1')
//     ->middleware(['auth:sanctum'])
//     ->group(function () {

//         // ── List & show ───────────────────────────────────────────────────

//         // GET /api/v1/me/notifications
//         // GET /api/v1/me/notifications?unread=1
//         // GET /api/v1/me/notifications?type=low_stock
//         // GET /api/v1/me/notifications?type=nearby_pharmacies
//         Route::get('me/notifications', [NotificationController::class, 'index'])
//             ->name('notifications.index');

//         // GET /api/v1/me/notifications/{id}  → auto-marks as read
//         Route::get('me/notifications/{id}', [NotificationController::class, 'show'])
//             ->name('notifications.show');

//         // ── Mark as read ──────────────────────────────────────────────────

//         // POST /api/v1/me/notifications/read-all
//         // Defined BEFORE /{id}/read to avoid 'read-all' matching as {id}
//         Route::post('me/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
//             ->name('notifications.read-all');

//         // POST /api/v1/me/notifications/{id}/read
//         Route::post('me/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
//             ->name('notifications.read');

//         // ── Delete ────────────────────────────────────────────────────────

//         // DELETE /api/v1/me/notifications
//         // Deletes all READ notifications — inbox cleanup
//         // Defined BEFORE /{id} to avoid '' matching as {id}
//         Route::delete('me/notifications', [NotificationController::class, 'destroyAll'])
//             ->name('notifications.destroy-all');

//         // DELETE /api/v1/me/notifications/{id}
//         Route::delete('me/notifications/{id}', [NotificationController::class, 'destroy'])
//             ->name('notifications.destroy');

//         // ── Client — nearby pharmacies ────────────────────────────────────

//         // POST /api/v1/me/nearby-pharmacies
//         // Auth: client only
//         // Body: { lat, lng, radius?, medicine? }
//         // Returns 202 Accepted — dispatches SendNearbyPharmaciesJob to queue
//         // Client receives result via GET /me/notifications
//         Route::post('me/nearby-pharmacies', [NotificationController::class, 'nearbyPharmacies'])
//             ->name('notifications.nearby-pharmacies');
//     });

// /*
// |--------------------------------------------------------------------------
// | User API Routes
// |--------------------------------------------------------------------------
// | Base URL: /api/v1
// | All routes require auth:sanctum
// |
// | Notification routes are in notification_routes.php
// */

// Route::prefix('v1')
//     ->middleware(['auth:sanctum'])
//     ->group(function () {

//         // ── Own profile ───────────────────────────────────────────────────

//         // GET    /api/v1/me
//         Route::get('me', [UserController::class, 'me'])
//             ->name('me');

//         // PUT/PATCH /api/v1/me
//         Route::match(['put', 'patch'], 'me', [UserController::class, 'update'])
//             ->defaults('user', null)
//             ->name('me.update');

//         // ── User management ───────────────────────────────────────────────

//         // GET    /api/v1/users          (super-admin only)
//         Route::get('users', [UserController::class, 'index'])
//             ->name('users.index');

//         // POST   /api/v1/users
//         Route::post('users', [UserController::class, 'store'])
//             ->name('users.store');

//         // GET    /api/v1/users/{user}
//         Route::get('users/{user}', [UserController::class, 'show'])
//             ->name('users.show');

//         // PUT/PATCH /api/v1/users/{user}
//         Route::match(['put', 'patch'], 'users/{user}', [UserController::class, 'update'])
//             ->name('users.update');

//         // DELETE /api/v1/users/{user}   (super-admin only)
//         Route::delete('users/{user}', [UserController::class, 'destroy'])
//             ->name('users.destroy');
//     });

// /*
// |--------------------------------------------------------------------------
// | Auth Routes
// |--------------------------------------------------------------------------
// | Base URL: /api/v1/auth
// |
// | These routes handle authentication ONLY.
// | Profile management → user_routes.php (UserController)
// | Notifications      → notification_routes.php (NotificationController)
// |
// | Role access:
// | ┌────────────────────────────┬───────────┬─────────────────────────────┐
// | │ Endpoint                   │ Auth      │ Who                         │
// | ├────────────────────────────┼───────────┼─────────────────────────────┤
// | │ POST /auth/login           │ public    │ all roles                   │
// | │ POST /auth/register        │ public    │ client self-registration    │
// | │ POST /auth/logout          │ sanctum   │ any authenticated user      │
// | │ POST /auth/logout-all      │ sanctum   │ any authenticated user      │
// | │ POST /auth/refresh         │ sanctum   │ any authenticated user      │
// | └────────────────────────────┴───────────┴─────────────────────────────┘
// |
// | Other role creation (NOT via register):
// |   super-admin    → AdminSeeder only (php artisan db:seed --class=AdminSeeder)
// |   pharmacy-owner → POST /api/v1/users  (super-admin)
// |   branch-manager → POST /api/v1/users  (super-admin | pharmacy-owner)
// */

// Route::prefix('v1/auth')->group(function () {

//     // ── Public ────────────────────────────────────────────────────────────

//     // POST /api/v1/auth/login
//     // All roles use this — response includes redirect hint per role
//     Route::post('login', [AuthController::class, 'login'])
//         ->name('auth.login');

//     // POST /api/v1/auth/register
//     // Client self-registration ONLY
//     Route::post('register', [AuthController::class, 'register'])
//         ->name('auth.register');

//     // ── Protected ─────────────────────────────────────────────────────────
//     Route::middleware(['auth:sanctum'])->group(function () {

//         // POST /api/v1/auth/logout
//         // Revokes current device token only
//         Route::post('logout', [AuthController::class, 'logout'])
//             ->name('auth.logout');

//         // POST /api/v1/auth/logout-all
//         // Revokes ALL tokens across all devices
//         Route::post('logout-all', [AuthController::class, 'logoutAll'])
//             ->name('auth.logout-all');

//         // POST /api/v1/auth/refresh
//         // Rotates token — revokes current, issues fresh
//         Route::post('refresh', [AuthController::class, 'refresh'])
//             ->name('auth.refresh');
//     });
// });



// //!─────────────────────Super Admin Start──────────────────────────//
// Route::middleware(['set_locale'])
//     ->prefix('v1')
//     ->name('api.')
//     ->group(function () {

//         // ---------------------------------------------------------------
//         // COUNTRIES
//         // GET    /api/v1/countries
//         // POST   /api/v1/countries
//         // GET    /api/v1/countries/{country}
//         // PUT    /api/v1/countries/{country}
//         // DELETE /api/v1/countries/{country}
//         // ---------------------------------------------------------------
//         Route::apiResource('countries', CountryController::class)
//             ->parameters(['countries' => 'country']);

//         // ---------------------------------------------------------------
//         // GOVERNORATES — shallow nested (store always needs a country)
//         //
//         // NESTED:
//         //   GET    /api/v1/countries/{country}/governorates   → index
//         //   POST   /api/v1/countries/{country}/governorates   → store
//         //
//         // NON-NESTED (shallow):
//         //   GET    /api/v1/governorates/{governorate}         → show
//         //   PUT    /api/v1/governorates/{governorate}         → update
//         //   DELETE /api/v1/governorates/{governorate}         → destroy
//         //
//         // EXTRA NON-NESTED:
//         //   GET    /api/v1/governorates                       → index all
//         // ---------------------------------------------------------------
//         Route::apiResource('countries.governorates', GovernorateController::class)
//             ->shallow()
//             ->parameters([
//                 'countries' => 'country',
//                 'governorates' => 'governorate',
//             ]);

//         // Standalone index — list ALL governorates across all countries
//         // ?filter[governorate_name]=Cairo
//         // ?filter[country_id]=1
//         // ?sort=governorate_slug
//         Route::get('governorates', [GovernorateController::class, 'indexAll'])
//             ->name('governorates.indexAll');

//         // ---------------------------------------------------------------
//         // CITIES — shallow nested (store always needs a governorate)
//         //
//         // NESTED:
//         //   GET    /api/v1/governorates/{governorate}/cities  → index
//         //   POST   /api/v1/governorates/{governorate}/cities  → store
//         //
//         // NON-NESTED (shallow):
//         //   GET    /api/v1/cities/{city}                      → show
//         //   PUT    /api/v1/cities/{city}                      → update
//         //   DELETE /api/v1/cities/{city}                      → destroy
//         //
//         // EXTRA NON-NESTED:
//         //   GET    /api/v1/cities                             → index all
//         // ---------------------------------------------------------------
//         Route::apiResource('governorates.cities', CityController::class)
//             ->shallow()
//             ->parameters([
//                 'governorates' => 'governorate',
//                 'cities' => 'city',
//             ]);
//         // Standalone index — list ALL cities across all governorates
//         // ?filter[city_name]=Nasr
//         // ?filter[governorate_id]=1
//         // ?sort=city_slug
//         Route::get('cities', [CityController::class, 'indexAll'])
//             ->name('cities.indexAll');

//         Route::apiResource('categories', CategoryController::class)
//             ->parameters(['categories' => 'category']);
//         // // All medicines across all categories
//         Route::get('medicines', [MedicineController::class, 'indexAll'])
//             ->name('medicines.indexAll');
//         // Route::apiResource('categories.medicines', MedicineController::class)
//         //     ->parameters(['medicines' => 'medicine'])
//         //     ->shallow();

//         // ── Public routes ────────────────────────────────────────────────────

//         // GET /api/v1/medicines
//         // Route::get('medicines', [MedicineController::class, 'index'])
//         //     ->name('medicines.index');

//         // GET /api/v1/medicines/search?q=para&locale=en
//         // Must be defined BEFORE {medicine} to avoid slug conflict
//         Route::get('medicines/search', [MedicineController::class, 'search'])
//             ->name('medicines.search');


//         // GET /api/v1/categories/{category}/medicines
//         Route::get('categories/{category}/medicines', [MedicineController::class, 'indexByCategory'])
//             ->name('categories.medicines.index');

//         // ── Protected routes (super-admin only) ──────────────────────────────
//         Route::middleware(['auth:sanctum'])->group(function () {

//             // POST /api/v1/medicines
//             Route::post('medicines', [MedicineController::class, 'store'])
//                 ->name('medicines.store');
//         });
//     });

// /*
// |--------------------------------------------------------------------------
// | Notification Routes
// |--------------------------------------------------------------------------
// | Base URL: /api/v1
// | All routes require auth:sanctum
// | Every user sees only their own notifications
// |
// | Notification types:
// |   low_stock          → branch-manager + pharmacy-owner (CheckLowStockJob)
// |   nearby_pharmacies  → client (SendNearbyPharmaciesJob)
// |
// | ┌────────────────────────────────────────┬──────────────────────────────┐
// | │ Endpoint                               │ Who                          │
// | ├────────────────────────────────────────┼──────────────────────────────┤
// | │ GET    /me/notifications               │ all roles                    │
// | │ GET    /me/notifications/{id}          │ all roles (auto-marks read)  │
// | │ POST   /me/notifications/read-all      │ all roles                    │
// | │ POST   /me/notifications/{id}/read     │ all roles                    │
// | │ DELETE /me/notifications               │ all roles (clears read only) │
// | │ DELETE /me/notifications/{id}          │ all roles                    │
// | │ POST   /me/nearby-pharmacies           │ client only                  │
// | └────────────────────────────────────────┴──────────────────────────────┘
// */

// Route::prefix('v1')
//     ->middleware(['auth:sanctum'])
//     ->group(function () {

//         // ── List & show ───────────────────────────────────────────────────

//         // GET /api/v1/me/notifications
//         // GET /api/v1/me/notifications?unread=1
//         // GET /api/v1/me/notifications?type=low_stock
//         // GET /api/v1/me/notifications?type=nearby_pharmacies
//         // GET /api/v1/me/notifications?per_page=20
//         Route::get('me/notifications', [NotificationController::class, 'index'])
//             ->name('notifications.index');

//         // GET /api/v1/me/notifications/{id}
//         // Auto-marks as read on open
//         Route::get('me/notifications/{id}', [NotificationController::class, 'show'])
//             ->name('notifications.show');

//         // ── Mark as read ──────────────────────────────────────────────────

//         // POST /api/v1/me/notifications/read-all
//         // MUST be defined before /{id}/read to prevent 'read-all' matching as {id}
//         Route::post('me/notifications/read-all', [NotificationController::class, 'markAllAsRead'])
//             ->name('notifications.read-all');

//         // POST /api/v1/me/notifications/{id}/read
//         Route::post('me/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
//             ->name('notifications.read');

//         // ── Delete ────────────────────────────────────────────────────────

//         // DELETE /api/v1/me/notifications
//         // Deletes all READ notifications only — unread are preserved
//         // MUST be defined before /{id} to prevent '' matching as {id}
//         Route::delete('me/notifications', [NotificationController::class, 'destroyAll'])
//             ->name('notifications.destroy-all');

//         // DELETE /api/v1/me/notifications/{id}
//         Route::delete('me/notifications/{id}', [NotificationController::class, 'destroy'])
//             ->name('notifications.destroy');

//         // ── Client — nearby pharmacies ────────────────────────────────────

//         // POST /api/v1/me/nearby-pharmacies
//         // Body: { lat, lng, radius?, medicine? }
//         // Auth: client only — returns 202 Accepted
//         // Result arrives as NearbyPharmaciesNotification via GET /me/notifications
//         Route::post('me/nearby-pharmacies', [NotificationController::class, 'nearbyPharmacies'])
//             ->name('notifications.nearby-pharmacies');
//     });

// /*
// |--------------------------------------------------------------------------
// | User Routes
// |--------------------------------------------------------------------------
// | Base URL: /api/v1
// | All routes require auth:sanctum
// |
// | Auth (login/register/logout/refresh) → auth_routes.php
// | Notifications                        → notification_routes.php
// |
// | Role access:
// | ┌──────────────────────────────┬────────────────────────────────────────┐
// | │ Endpoint                     │ Who                                    │
// | ├──────────────────────────────┼────────────────────────────────────────┤
// | │ GET    /me                   │ any authenticated user (own profile)   │
// | │ PUT/PATCH /me                │ any authenticated user (own profile)   │
// | │ GET    /users                │ super-admin only                       │
// | │ POST   /users                │ super-admin | pharmacy-owner           │
// | │ GET    /users/{user}         │ super-admin | own account              │
// | │ PUT/PATCH /users/{user}      │ super-admin | pharmacy-owner | own     │
// | │ DELETE /users/{user}         │ super-admin only                       │
// | └──────────────────────────────┴────────────────────────────────────────┘
// */

// Route::prefix('v1')
//     ->middleware(['auth:sanctum'])
//     ->group(function () {

//         // ── Own profile ───────────────────────────────────────────────────

//         // GET /api/v1/me
//         // Returns full UserResource with role booleans, unread count, pharmacy, branch
//         Route::get('me', [UserController::class, 'me'])
//             ->name('me');

//         // PUT/PATCH /api/v1/me
//         // Update own name, email, password (role/pharmacy/branch blocked unless admin)
//         Route::match(['put', 'patch'], 'me', [UserController::class, 'updateMe'])
//             ->name('me.update');

//         // ── User management ───────────────────────────────────────────────

//         // GET /api/v1/users
//         // ?filter[role]=branch-manager  ?filter[of_pharmacy]=1  ?filter[of_branch]=3
//         // ?include=pharmacy,branch
//         Route::get('users', [UserController::class, 'index'])
//             ->name('users.index');

//         // POST /api/v1/users
//         // Body: { name, email, password, role, pharmacy_id?, branch_id? }
//         Route::post('users', [UserController::class, 'store'])
//             ->name('users.store');

//         // GET /api/v1/users/{user}
//         Route::get('users/{user}', [UserController::class, 'show'])
//             ->name('users.show');

//         // PUT/PATCH /api/v1/users/{user}
//         Route::match(['put', 'patch'], 'users/{user}', [UserController::class, 'update'])
//             ->name('users.update');

//         // DELETE /api/v1/users/{user}
//         Route::delete('users/{user}', [UserController::class, 'destroy'])
//             ->name('users.destroy');
//     });

// //!─────────────────────Option1────────────────────────────//


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
use Illuminate\Support\Facades\Route;

/*
|==========================================================================
| API Routes — v1
|==========================================================================
|
| All routes live under /api/v1
| Locale middleware (set_locale) applied globally via the group below
|
| Role creation flow:
|   super-admin    → AdminSeeder only  (never public)
|   pharmacy-owner → POST /v1/users    (super-admin only)
|   branch-manager → POST /v1/users    (super-admin | pharmacy-owner)
|   client         → POST /v1/auth/register  (public)
|
| Route order rules (slug conflicts):
|   /search and /nearby MUST be defined BEFORE /{slug} routes
|   /read-all MUST be defined BEFORE /{id}/read
|   /me/notifications MUST be defined BEFORE /me/{anything}
|
*/

Route::prefix('v1')
    ->middleware(['set_locale'])
    ->group(function () {

        // ==================================================================
        // AUTH — public + protected
        // ==================================================================
        // POST   /v1/auth/login          → all roles
        // POST   /v1/auth/register       → client self-registration only
        // POST   /v1/auth/logout         → auth required
        // POST   /v1/auth/logout-all     → auth required
        // POST   /v1/auth/refresh        → auth required
        // ==================================================================

        Route::prefix('auth')->name('auth.')->group(function () {

            // ── Public ────────────────────────────────────────────────────
            Route::post('login',    [AuthController::class, 'login'])->name('login');
            Route::post('register', [AuthController::class, 'register'])->name('register');

            // ── Protected ─────────────────────────────────────────────────
            Route::middleware('auth:sanctum')->group(function () {
                Route::post('logout',     [AuthController::class, 'logout'])->name('logout');
                Route::post('logout-all', [AuthController::class, 'logoutAll'])->name('logout-all');
                Route::post('refresh',    [AuthController::class, 'refresh'])->name('refresh');
            });
        });

        // ==================================================================
        // GEOGRAPHY — public read, admin write
        // Nested (shallow): country → governorate → city
        // ==================================================================

        // ── Countries ─────────────────────────────────────────────────────
        // GET    /v1/countries
        // POST   /v1/countries              (auth: super-admin)
        // GET    /v1/countries/{country}
        // PUT    /v1/countries/{country}    (auth: super-admin)
        // DELETE /v1/countries/{country}    (auth: super-admin)
        // ------------------------------------------------------------------
        Route::apiResource('countries', CountryController::class)
            ->parameters(['countries' => 'country'])
            ->names('countries');

        // ── Governorates — shallow nested under countries ──────────────────
        // NESTED   GET  /v1/countries/{country}/governorates   → index
        // NESTED   POST /v1/countries/{country}/governorates   → store (auth: super-admin)
        // SHALLOW  GET  /v1/governorates/{governorate}         → show
        // SHALLOW  PUT  /v1/governorates/{governorate}         → update (auth: super-admin)
        // SHALLOW  DEL  /v1/governorates/{governorate}         → destroy (auth: super-admin)
        // EXTRA    GET  /v1/governorates                       → index all
        //          ?filter[country_id]=1  ?filter[governorate_name]=Cairo  ?sort=governorate_slug
        // ------------------------------------------------------------------
        Route::apiResource('countries.governorates', GovernorateController::class)
            ->shallow()
            ->parameters(['countries' => 'country', 'governorates' => 'governorate'])
            ->names('governorates');

        Route::get('governorates', [GovernorateController::class, 'indexAll'])
            ->name('governorates.all');

        // ── Cities — shallow nested under governorates ─────────────────────
        // NESTED   GET  /v1/governorates/{governorate}/cities  → index
        // NESTED   POST /v1/governorates/{governorate}/cities  → store (auth: super-admin)
        // SHALLOW  GET  /v1/cities/{city}                      → show
        // SHALLOW  PUT  /v1/cities/{city}                      → update (auth: super-admin)
        // SHALLOW  DEL  /v1/cities/{city}                      → destroy (auth: super-admin)
        // EXTRA    GET  /v1/cities                             → index all
        //          ?filter[governorate_id]=1  ?filter[city_name]=Nasr  ?sort=city_slug
        // ------------------------------------------------------------------
        Route::apiResource('governorates.cities', CityController::class)
            ->shallow()
            ->parameters(['governorates' => 'governorate', 'cities' => 'city'])
            ->names('cities');

        Route::get('cities', [CityController::class, 'indexAll'])
            ->name('cities.all');

        // ==================================================================
        // CATEGORIES — public read, admin write
        // GET    /v1/categories
        // POST   /v1/categories          (auth: super-admin)
        // GET    /v1/categories/{category}
        // PUT    /v1/categories/{category}   (auth: super-admin)
        // DELETE /v1/categories/{category}   (auth: super-admin)
        // ==================================================================
        Route::apiResource('categories', CategoryController::class)
            ->parameters(['categories' => 'category'])
            ->names('categories');

        // ==================================================================
        // MEDICINES — public read, admin write
        //
        // Non-nested routes (⚠ search BEFORE {medicine}):
        //   GET  /v1/medicines                      → all medicines
        //   GET  /v1/medicines/search?q=para        → search (locale-aware)
        //   GET  /v1/medicines/{medicine}           → show by slug
        //   POST /v1/medicines                      (auth: super-admin)
        //   PUT  /v1/medicines/{medicine}           (auth: super-admin)
        //   DEL  /v1/medicines/{medicine}           (auth: super-admin)
        //
        // Nested under category:
        //   GET  /v1/categories/{category}/medicines → medicines in category
        // ==================================================================

        // ── search MUST come before {medicine} ────────────────────────────
        Route::get('medicines/search', [MedicineController::class, 'search'])
            ->name('medicines.search');

        // ── Public ────────────────────────────────────────────────────────
        Route::get('medicines',          [MedicineController::class, 'index'])->name('medicines.index');
        Route::get('medicines/{medicine}', [MedicineController::class, 'show'])->name('medicines.show');
        Route::get('categories/{category}/medicines', [MedicineController::class, 'indexByCategory'])
            ->name('categories.medicines.index');

        // ── Protected ─────────────────────────────────────────────────────
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('medicines',                                    [MedicineController::class, 'store'])->name('medicines.store');
            Route::match(['put', 'patch'], 'medicines/{medicine}',       [MedicineController::class, 'update'])->name('medicines.update');
            Route::delete('medicines/{medicine}',                       [MedicineController::class, 'destroy'])->name('medicines.destroy');
        });

        // ==================================================================
        // PHARMACIES — public read, role-restricted write
        //
        // Non-nested (⚠ search MUST come before {pharmacy}):
        //   GET  /v1/pharmacies                             → index
        //   GET  /v1/pharmacies/search?q=cairo             → search
        //   GET  /v1/pharmacies/{pharmacy}                  → show
        //   GET  /v1/pharmacies/{pharmacy}/medicines        → medicines in pharmacy
        //   GET  /v1/pharmacies/{pharmacy}/nearest-branch   → nearest branch to coords
        //   POST /v1/pharmacies                             (auth: super-admin)
        //   PUT  /v1/pharmacies/{pharmacy}                  (auth: super-admin | owner)
        //   DEL  /v1/pharmacies/{pharmacy}                  (auth: super-admin)
        //
        // Protected sub-resources:
        //   GET  /v1/pharmacies/{pharmacy}/low-stock        (auth: super-admin | owner)
        //   GET  /v1/pharmacies/{pharmacy}/expiring-soon    (auth: super-admin | owner)
        // ==================================================================

        // ── search MUST come before {pharmacy} ────────────────────────────
        Route::get('pharmacies/search', [PharmacyController::class, 'search'])
            ->name('pharmacies.search');

        // ── Public ────────────────────────────────────────────────────────
        Route::get('pharmacies',                            [PharmacyController::class, 'index'])->name('pharmacies.index');
        Route::get('pharmacies/{pharmacy}',                 [PharmacyController::class, 'show'])->name('pharmacies.show');
        Route::get('pharmacies/{pharmacy}/medicines',       [PharmacyController::class, 'medicines'])->name('pharmacies.medicines');
        Route::get('pharmacies/{pharmacy}/nearest-branch',  [PharmacyController::class, 'nearestBranch'])->name('pharmacies.nearest-branch');

        // ── Protected ─────────────────────────────────────────────────────
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('pharmacies',                                   [PharmacyController::class, 'store'])->name('pharmacies.store');
            Route::match(['put', 'patch'], 'pharmacies/{pharmacy}',      [PharmacyController::class, 'update'])->name('pharmacies.update');
            Route::delete('pharmacies/{pharmacy}',                      [PharmacyController::class, 'destroy'])->name('pharmacies.destroy');
            Route::get('pharmacies/{pharmacy}/low-stock',               [PharmacyController::class, 'lowStock'])->name('pharmacies.low-stock');
            Route::get('pharmacies/{pharmacy}/expiring-soon',           [PharmacyController::class, 'expiringSoon'])->name('pharmacies.expiring-soon');
        });

        // ==================================================================
        // BRANCHES — public read, role-restricted write
        //
        // Non-nested (⚠ search + nearby MUST come before {branch}):
        //   GET  /v1/branches                               → index
        //   GET  /v1/branches/search?q=cairo               → search
        //   GET  /v1/branches/nearby?lat=..&lng=..          → nearby
        //   GET  /v1/branches/{branch}                      → show
        //   GET  /v1/branches/{branch}/medicines            → medicines in branch
        //
        // Nested under pharmacy:
        //   GET  /v1/pharmacies/{pharmacy}/branches         → branches of pharmacy
        //
        // Protected:
        //   POST /v1/branches                               (auth: super-admin | owner)
        //   PUT  /v1/branches/{branch}                      (auth: super-admin | owner | manager)
        //   DEL  /v1/branches/{branch}                      (auth: super-admin | owner)
        //   GET  /v1/branches/{branch}/low-stock            (auth: super-admin | owner | manager)
        //   GET  /v1/branches/{branch}/expiring-soon        (auth: super-admin | owner | manager)
        // ==================================================================

        // ── search + nearby MUST come before {branch} ─────────────────────
        Route::get('branches/search', [BranchController::class, 'search'])
            ->name('branches.search');
        Route::get('branches/nearby', [BranchController::class, 'nearby'])
            ->name('branches.nearby');

        // ── Public ────────────────────────────────────────────────────────
        Route::get('branches',                          [BranchController::class, 'index'])->name('branches.index');
        Route::get('branches/{branch}',                 [BranchController::class, 'show'])->name('branches.show');
        Route::get('branches/{branch}/medicines',       [BranchController::class, 'medicines'])->name('branches.medicines');
        Route::get('pharmacies/{pharmacy}/branches',    [BranchController::class, 'indexByPharmacy'])->name('pharmacies.branches.index');

        // ── Protected ─────────────────────────────────────────────────────
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('branches',                                 [BranchController::class, 'store'])->name('branches.store');
            Route::match(['put', 'patch'], 'branches/{branch}',      [BranchController::class, 'update'])->name('branches.update');
            Route::delete('branches/{branch}',                      [BranchController::class, 'destroy'])->name('branches.destroy');
            Route::get('branches/{branch}/low-stock',               [BranchController::class, 'lowStock'])->name('branches.low-stock');
            Route::get('branches/{branch}/expiring-soon',           [BranchController::class, 'expiringSoon'])->name('branches.expiring-soon');
        });

        // ==================================================================
        // PROFILE — authenticated users only
        //
        //   GET      /v1/me           → own profile
        //   PUT|PATCH /v1/me          → update own profile
        //
        // USER MANAGEMENT — role-restricted
        //   GET    /v1/users                    (auth: super-admin)
        //   POST   /v1/users                    (auth: super-admin | owner)
        //   GET    /v1/users/{user}             (auth: super-admin | own)
        //   PUT    /v1/users/{user}             (auth: super-admin | owner | own)
        //   DELETE /v1/users/{user}             (auth: super-admin)
        // ==================================================================
        Route::middleware('auth:sanctum')->group(function () {

            // Own profile
            Route::get('me',                           [UserController::class, 'me'])->name('me');
            Route::match(['put', 'patch'], 'me',        [UserController::class, 'updateMe'])->name('me.update');

            // User management
            Route::get('users',                        [UserController::class, 'index'])->name('users.index');
            Route::post('users',                       [UserController::class, 'store'])->name('users.store');
            Route::get('users/{user}',                 [UserController::class, 'show'])->name('users.show');
            Route::match(['put', 'patch'], 'users/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('users/{user}',              [UserController::class, 'destroy'])->name('users.destroy');
        });

        // ==================================================================
        // NOTIFICATIONS — authenticated users only
        //
        // ⚠ Route order matters:
        //   /read-all MUST be before /{id}/read
        //   DELETE /me/notifications (all) MUST be before /{id}
        //
        //   GET    /v1/me/notifications               → list (all roles)
        //   GET    /v1/me/notifications/{id}          → show + auto-mark read
        //   POST   /v1/me/notifications/read-all      → mark all read
        //   POST   /v1/me/notifications/{id}/read     → mark one read
        //   DELETE /v1/me/notifications               → clear all read
        //   DELETE /v1/me/notifications/{id}          → delete one
        //   POST   /v1/me/nearby-pharmacies           → client only → 202
        // ==================================================================
        Route::middleware('auth:sanctum')->group(function () {

            // ⚠ read-all BEFORE /{id}/read
            Route::post('me/notifications/read-all',        [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
            Route::post('me/notifications/{id}/read',       [NotificationController::class, 'markAsRead'])->name('notifications.read');

            // ⚠ DELETE all BEFORE DELETE /{id}
            Route::delete('me/notifications',               [NotificationController::class, 'destroyAll'])->name('notifications.destroy-all');
            Route::delete('me/notifications/{id}',          [NotificationController::class, 'destroy'])->name('notifications.destroy');

            // List + show
            Route::get('me/notifications',                  [NotificationController::class, 'index'])->name('notifications.index');
            Route::get('me/notifications/{id}',             [NotificationController::class, 'show'])->name('notifications.show');

            // Client nearby pharmacies
            Route::post('me/nearby-pharmacies',             [NotificationController::class, 'nearbyPharmacies'])->name('notifications.nearby-pharmacies');
        });
    }); // end v1 + set_locale
 
// //*─────────────────────Pharmacy Owner Start──────────────────────────//

// //*─────────────────────Pharmacy Owner End────────────────────────────//

// //=─────────────────────Branch Manager Start──────────────────────────//

// //=─────────────────────Branch Manager End────────────────────────────//

// //TODO─────────────────────Client Start──────────────────────────//

// //TODO─────────────────────Client End────────────────────────────//

//!------------------------------
//!------------------------------
//!------------------------------
//!------------------------------

// //TODO─────────────────────Option2────────────────────────────//

// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\Api\{
//     Auth\AuthController,
//     CountryController,
//     GovernorateController,
//     CityController,
//     CategoryController,
//     MedicineController,
//     PharmacyController,
//     BranchController,
//     NotificationController,
//     UserController
// };

// Route::prefix('v1')->middleware(['set_locale'])->group(function () {

//     /*
//     |--------------------------------------------------
//     | AUTH (PUBLIC)
//     |--------------------------------------------------
//     */
//     Route::prefix('auth')->group(function () {
//         Route::post('login', [AuthController::class, 'login']);
//         Route::post('register', [AuthController::class, 'register']);
//     });

//     /*
//     |--------------------------------------------------
//     | LOCATION (Nested + Shallow)
//     |--------------------------------------------------
//     */

//     // Countries (public read)
//     Route::apiResource('countries', CountryController::class) //*done
//         ->only(['index', 'show']);

//     // Governorates (nested under country + shallow)
//     Route::apiResource('countries.governorates', GovernorateController::class)
//         ->shallow()
//         ->only(['index', 'show']);

//     // All governorates (non-nested)
//     Route::get('governorates', [GovernorateController::class, 'indexAll']); //*done

//     // Cities (nested under governorate + shallow)
//     Route::apiResource('governorates.cities', CityController::class)
//         ->shallow()
//         ->only(['index', 'show']);

//     // All cities (non-nested)
//     Route::get('cities', [CityController::class, 'indexAll']); //*done

//     /*
//     |--------------------------------------------------
//     | CATEGORIES & MEDICINES
//     |--------------------------------------------------
//     */

//     Route::apiResource('categories', CategoryController::class)  //*done
//         ->only(['index', 'show']);

//     Route::get('medicines', [MedicineController::class, 'index']); //*done
//     Route::get('medicines/search', [MedicineController::class, 'search']); // BEFORE {medicine} //*done
//     Route::get('medicines/{medicine}', [MedicineController::class, 'show']); //*done

//     // Nested
//     Route::get('categories/{category}/medicines', [MedicineController::class, 'indexByCategory']); //*done

//     /*
//     |--------------------------------------------------
//     | PHARMACIES
//     |--------------------------------------------------
//     */

//     Route::get('pharmacies', [PharmacyController::class, 'index']); //*done
//     Route::get('pharmacies/search', [PharmacyController::class, 'search']); //*done
//     Route::get('pharmacies/{pharmacy}', [PharmacyController::class, 'show']); //*done

//     // Nested
//     Route::get('pharmacies/{pharmacy}/medicines', [PharmacyController::class, 'medicines']); //*done
//     Route::get('pharmacies/{pharmacy}/branches', [BranchController::class, 'indexByPharmacy']); //*done

//     Route::get('pharmacies/{pharmacy}/nearest-branch', [PharmacyController::class, 'nearestBranch']);

//     /*
//     |--------------------------------------------------
//     | BRANCHES
//     |--------------------------------------------------
//     */

//     Route::get('branches', [BranchController::class, 'index']); //*done
//     Route::get('branches/search', [BranchController::class, 'search']); // BEFORE {branch} //*done
//     Route::get('branches/nearby', [BranchController::class, 'nearby']); // BEFORE {branch}
//     Route::get('branches/{branch}', [BranchController::class, 'show']); //*done

//     /*
//     |--------------------------------------------------
//     | AUTHENTICATED USERS
//     |--------------------------------------------------
//     */
//     Route::middleware(['auth:sanctum'])->group(function () {

//         /*
//         |-------------------------
//         | AUTH
//         |-------------------------
//         */
//         Route::post('auth/logout', [AuthController::class, 'logout']);
//         Route::post('auth/logout-all', [AuthController::class, 'logoutAll']);
//         Route::post('auth/refresh', [AuthController::class, 'refresh']);

//         /*
//         |-------------------------
//         | PROFILE
//         |-------------------------
//         */
//         Route::get('me', [UserController::class, 'me']);
//         Route::match(['put', 'patch'], 'me', [UserController::class, 'update']);

//         /*
//         |-------------------------
//         | NOTIFICATIONS (ALL ROLES)
//         |-------------------------
//         */
//         Route::prefix('me/notifications')->group(function () {
//             Route::get('/', [NotificationController::class, 'index']);
//             Route::get('{id}', [NotificationController::class, 'show']);
//             Route::post('read-all', [NotificationController::class, 'markAllAsRead']);
//             Route::post('{id}/read', [NotificationController::class, 'markAsRead']);
//             Route::delete('/', [NotificationController::class, 'destroyAll']);
//             Route::delete('{id}', [NotificationController::class, 'destroy']);
//         });

//         /*
//         |--------------------------------------------------
//         | SUPER ADMIN
//         |--------------------------------------------------
//         */
//         Route::middleware(['role:super-admin'])->group(function () {

//             // Full control over locations
//             Route::apiResource('countries', CountryController::class)->except(['index', 'show']);
//             Route::apiResource('countries.governorates', GovernorateController::class)
//                 ->shallow()
//                 ->except(['index', 'show']);
//             Route::apiResource('governorates.cities', CityController::class)
//                 ->shallow()
//                 ->except(['index', 'show']);

//             // Categories & medicines
//             Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);

//             Route::post('medicines', [MedicineController::class, 'store']);
//             Route::match(['put', 'patch'], 'medicines/{medicine}', [MedicineController::class, 'update']);
//             Route::delete('medicines/{medicine}', [MedicineController::class, 'destroy']);

//             // Pharmacies
//             Route::post('pharmacies', [PharmacyController::class, 'store']);
//             Route::delete('pharmacies/{pharmacy}', [PharmacyController::class, 'destroy']);

//             // Users
//             Route::get('users', [UserController::class, 'index']);
//             Route::delete('users/{user}', [UserController::class, 'destroy']);
//         });

//         /*
//         |--------------------------------------------------
//         | PHARMACY OWNER
//         |--------------------------------------------------
//         */
//         Route::middleware(['role:pharmacy-owner'])->group(function () {

//             Route::post('pharmacies', [PharmacyController::class, 'store']);
//             Route::match(['put', 'patch'], 'pharmacies/{pharmacy}', [PharmacyController::class, 'update']);

//             Route::post('branches', [BranchController::class, 'store']);
//             Route::match(['put', 'patch'], 'branches/{branch}', [BranchController::class, 'update']);

//             Route::post('users', [UserController::class, 'store']); // create managers
//         });

//         /*
//         |--------------------------------------------------
//         | BRANCH MANAGER
//         |--------------------------------------------------
//         */
//         Route::middleware(['role:branch-manager'])->group(function () {

//             Route::get('branches/{branch}/low-stock', [BranchController::class, 'lowStock']);
//             Route::get('branches/{branch}/expiring-soon', [BranchController::class, 'expiringSoon']);
//         });

//         /*
//         |--------------------------------------------------
//         | CLIENT
//         |--------------------------------------------------
//         */
//         Route::middleware(['role:client'])->group(function () {

//             Route::post('me/nearby-pharmacies', [NotificationController::class, 'nearbyPharmacies']);
//         });
//     });
// });
