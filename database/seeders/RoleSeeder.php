<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * RoleSeeder
 *
 * Creates all roles and permissions used by Spatie Permission.
 * MUST run before all other auth seeders.
 *
 * Run:
 *   php artisan db:seed --class=RoleSeeder
 *
 * Roles:
 *   super-admin     → full system access
 *   pharmacy-owner  → own pharmacy + branches + managers
 *   branch-manager  → own branch + inventory
 *   client          → search + nearby pharmacies
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Define all permissions ─────────────────────────────────────────

        $permissions = [
            // Geography (super-admin only)
            'manage-countries',
            'manage-governorates',
            'manage-cities',

            // Categories (super-admin only)
            'manage-categories',

            // Medicines (super-admin only)
            'manage-medicines',
            'view-medicines',

            // Pharmacies
            'manage-pharmacies',       // super-admin
            'view-pharmacy',           // pharmacy-owner (own)
            'update-pharmacy',         // pharmacy-owner (own)

            // Branches
            'manage-branches',         // super-admin
            'create-branch',           // pharmacy-owner
            'update-branch',           // pharmacy-owner | branch-manager (own)
            'delete-branch',           // pharmacy-owner
            'view-branch',             // pharmacy-owner | branch-manager

            // Inventory
            'manage-inventory',        // branch-manager
            'view-inventory',          // branch-manager | pharmacy-owner
            'view-low-stock',          // branch-manager | pharmacy-owner

            // Users
            'manage-users',            // super-admin
            'create-branch-manager',   // pharmacy-owner
            'view-staff',              // pharmacy-owner

            // Notifications
            'receive-low-stock-alerts',   // branch-manager | pharmacy-owner
            'receive-nearby-alerts',      // client
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name'       => $permission,
                'guard_name' => 'api',
            ]);
        }

        $this->command->info('✅ Permissions created: ' . count($permissions));

        // ── Create roles and assign permissions ───────────────────────────

        // SUPER-ADMIN — all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'api']);
        $superAdmin->syncPermissions($permissions);
        $this->command->info('✅ Role created: super-admin  (' . count($permissions) . ' permissions)');

        // PHARMACY-OWNER — own pharmacy, branches, staff, inventory alerts
        $pharmacyOwner = Role::firstOrCreate(['name' => 'pharmacy-owner', 'guard_name' => 'api']);
        $pharmacyOwner->syncPermissions([
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
        $this->command->info('✅ Role created: pharmacy-owner  (12 permissions)');

        // BRANCH-MANAGER — own branch inventory + alerts
        $branchManager = Role::firstOrCreate(['name' => 'branch-manager', 'guard_name' => 'api']);
        $branchManager->syncPermissions([
            'view-medicines',
            'view-branch',
            'manage-inventory',
            'view-inventory',
            'view-low-stock',
            'receive-low-stock-alerts',
        ]);
        $this->command->info('✅ Role created: branch-manager  (6 permissions)');

        // CLIENT — browse, search, nearby pharmacies
        $client = Role::firstOrCreate(['name' => 'client', 'guard_name' => 'api']);
        $client->syncPermissions([
            'view-medicines',
            'receive-nearby-alerts',
        ]);
        $this->command->info('✅ Role created: client  (2 permissions)');
    }
}
