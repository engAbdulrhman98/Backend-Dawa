<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Branch;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * AuthSeeder
 *
 * Single seeder that handles everything auth-related:
 *   1. Creates all Spatie roles + permissions
 *   2. Creates one test account per role
 *
 * ──────────────────────────────────────────────────────────
 * Run:
 * ──────────────────────────────────────────────────────────
 *   php artisan db:seed --class=AuthSeeder
 *
 * ──────────────────────────────────────────────────────────
 * Production setup (call from DatabaseSeeder):
 * ──────────────────────────────────────────────────────────
 *   $this->call(AuthSeeder::class);
 *
 * ──────────────────────────────────────────────────────────
 * .env keys used:
 * ──────────────────────────────────────────────────────────
 *   ADMIN_NAME="System Admin"
 *   ADMIN_EMAIL="admin@pharmacy.com"
 *   ADMIN_PASSWORD="Admin@12345"
 *
 * ──────────────────────────────────────────────────────────
 * Role creation in production (NOT via this seeder):
 * ──────────────────────────────────────────────────────────
 *   super-admin    → this seeder only  (never via API)
 *   pharmacy-owner → POST /api/v1/users  (super-admin)
 *   branch-manager → POST /api/v1/users  (super-admin | pharmacy-owner)
 *   client         → POST /api/v1/auth/register  (public)
 *
 * ⚠️  Test accounts below are for development only.
 *     Change all passwords before going to production.
 */
class AuthSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command->info('');
        $this->command->info('🌱 AuthSeeder starting...');
        $this->command->info('');

        // ──────────────────────────────────────────────────
        // STEP 1 — Create permissions
        // ──────────────────────────────────────────────────

        $permissions = [
            // Geography
            'manage-countries',
            'manage-governorates',
            'manage-cities',

            // Categories
            'manage-categories',

            // Medicines
            'manage-medicines',
            'view-medicines',

            // Pharmacies
            'manage-pharmacies',
            'view-pharmacy',
            'update-pharmacy',

            // Branches
            'manage-branches',
            'create-branch',
            'update-branch',
            'delete-branch',
            'view-branch',

            // Inventory
            'manage-inventory',
            'view-inventory',
            'view-low-stock',

            // Users
            'manage-users',
            'create-branch-manager',
            'view-staff',

            // Notifications
            'receive-low-stock-alerts',
            'receive-nearby-alerts',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => 'web',
            ]);
        }

        $this->command->info('✅ Permissions created: ' . count($permissions));

        // ──────────────────────────────────────────────────
        // STEP 2 — Create roles and assign permissions
        // ──────────────────────────────────────────────────

        // SUPER-ADMIN — full access to everything
        $superAdminRole = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions($permissions);

        // PHARMACY-OWNER — own pharmacy, branches, staff, inventory
        $ownerRole = Role::firstOrCreate(['name' => 'pharmacy-owner', 'guard_name' => 'web']);
        $ownerRole->syncPermissions([
            'view-medicines',
            'view-pharmacy',
            'update-pharmacy',
            'create-branch',
            'update-branch',
            'delete-branch',
            'view-branch',
            'view-inventory',
            'view-low-stock',
            'create-branch-manager',
            'view-staff',
            'receive-low-stock-alerts',
        ]);

        // BRANCH-MANAGER — own branch + inventory alerts
        $managerRole = Role::firstOrCreate(['name' => 'branch-manager', 'guard_name' => 'web']);
        $managerRole->syncPermissions([
            'view-medicines',
            'view-branch',
            'manage-inventory',
            'view-inventory',
            'view-low-stock',
            'receive-low-stock-alerts',
        ]);

        // CLIENT — browse medicines + nearby pharmacies
        $clientRole = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'web']);
        $clientRole->syncPermissions([
            'view-medicines',
            'receive-nearby-alerts',
        ]);

        $this->command->info('✅ Roles created: super-admin, pharmacy-owner, branch-manager, client');

        // ──────────────────────────────────────────────────
        // STEP 3 — Super admin account (from .env)
        // ──────────────────────────────────────────────────

        $admin = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@pharmacy.com')],
            [
                'name'        => env('ADMIN_NAME', 'System Admin'),
                'password'    => Hash::make(env('ADMIN_PASSWORD', 'Admin@12345')),
                'pharmacy_id' => null,
                'branch_id'   => null,
            ]
        );

        if (!$admin->hasRole('super-admin')) {
            $admin->assignRole('super-admin');
        }

        $this->command->info("✅ Super admin: {$admin->email}");

        // ──────────────────────────────────────────────────
        // STEP 4 — Test pharmacy-owner
        // Dev/testing only — in production create via POST /api/v1/users
        // ──────────────────────────────────────────────────

        // Create a test pharmacy for the owner
        $pharmacy = Pharmacy::firstOrCreate(
            ['pharmacy_slug' => 'test-pharmacy'],
            [
                'pharmacy_name' => [
                    'en' => 'Test Pharmacy',
                    'ar' => 'صيدلية تجريبية',
                ],
            ]
        );

        $owner = User::firstOrCreate(
            ['email' => 'owner@pharmacy.com'],
            [
                'name'        => 'Pharmacy Owner',
                'password'    => Hash::make('Owner@12345'),
                'pharmacy_id' => $pharmacy->id,
                'branch_id'   => null,
            ]
        );

        if (!$owner->pharmacy_id) {
            $owner->update(['pharmacy_id' => $pharmacy->id]);
        }

        if (!$owner->hasRole('pharmacy-owner')) {
            $owner->assignRole('pharmacy-owner');
        }

        $this->command->info("✅ Pharmacy owner: {$owner->email}");

        // ──────────────────────────────────────────────────
        // STEP 5 — Test branch-manager
        // Dev/testing only — in production create via POST /api/v1/users
        // ──────────────────────────────────────────────────

        // Create a test branch for the manager
        $branch = Branch::firstOrCreate(
            ['branch_slug' => 'test-pharmacy-downtown'],
            [
                'pharmacy_id'    => $pharmacy->id,
                'city_id'        => 1, // update after running geography seeders
                'branch_name'    => [
                    'en' => 'Test Pharmacy - Downtown',
                    'ar' => 'صيدلية تجريبية - وسط المدينة',
                ],
                'branch_address' => [
                    'en' => '123 Downtown Street, Cairo',
                    'ar' => '١٢٣ شارع وسط المدينة، القاهرة',
                ],
                'branch_phone'   => [
                    'en' => '+20 2 1234 5678',
                    'ar' => '+20 2 1234 5678',
                ],
                // Coordinates auto-filled by spatie/geocoder via Branch::booted()
                'latitude'  => null,
                'longitude' => null,
            ]
        );

        $manager = User::firstOrCreate(
            ['email' => 'manager@pharmacy.com'],
            [
                'name'        => 'Branch Manager',
                'password'    => Hash::make('Manager@12345'),
                'pharmacy_id' => $pharmacy->id,
                'branch_id'   => $branch->id,
            ]
        );

        if (!$manager->pharmacy_id || !$manager->branch_id) {
            $manager->update([
                'pharmacy_id' => $pharmacy->id,
                'branch_id'   => $branch->id,
            ]);
        }

        if (!$manager->hasRole('branch-manager')) {
            $manager->assignRole('branch-manager');
        }

        $this->command->info("✅ Branch manager: {$manager->email}");

        // ──────────────────────────────────────────────────
        // STEP 6 — Test clients
        // Dev/testing only — in production clients self-register
        // via POST /api/v1/auth/register
        // ──────────────────────────────────────────────────

        $clientsData = [
            ['name' => 'Test Client',   'email' => 'client@pharmacy.com'],
            ['name' => 'Test Client 2', 'email' => 'client2@pharmacy.com'],
        ];

        foreach ($clientsData as $data) {
            $client = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'        => $data['name'],
                    'password'    => Hash::make('Client@12345'),
                    'pharmacy_id' => null,
                    'branch_id'   => null,
                ]
            );

            if (!$client->hasRole('client')) {
                $client->assignRole('client');
            }
        }

        $this->command->info('✅ Clients: client@pharmacy.com, client2@pharmacy.com');

        // ──────────────────────────────────────────────────
        // Summary
        // ──────────────────────────────────────────────────

        $this->command->info('');
        $this->command->table(
            ['Role', 'Email', 'Password', 'Notes'],
            [
                ['super-admin',    env('ADMIN_EMAIL', 'admin@pharmacy.com'), env('ADMIN_PASSWORD', 'Admin@12345'), 'From .env — change immediately'],
                ['pharmacy-owner', 'owner@pharmacy.com',   'Owner@12345',   'Linked to Test Pharmacy'],
                ['branch-manager', 'manager@pharmacy.com', 'Manager@12345', 'Linked to Downtown branch'],
                ['client',         'client@pharmacy.com',  'Client@12345',  'Test client account'],
                ['client',         'client2@pharmacy.com', 'Client@12345',  'Test client account'],
            ]
        );
        $this->command->warn('⚠️  Change all passwords before going to production.');
        $this->command->warn('⚠️  Update branch city_id after running geography seeders.');
    }
}
