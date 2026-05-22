<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


/**
 * AdminSeeder
 *
 * Creates the super-admin account.
 * Requires RoleSeeder to have run first.
 *
 * Credentials loaded from .env:
 *   ADMIN_NAME="System Admin"
 *   ADMIN_EMAIL="admin@pharmacy.com"
 *   ADMIN_PASSWORD="Admin@12345"
 *
 * Run:
 *   php artisan db:seed --class=AdminSeeder
 *
 * ⚠️ Change the password immediately after first login.
 * ⚠️ This user is NEVER created via any API endpoint.
 *    The /register route is for clients only.
 *    Super-admin exists only via this seeder.
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@pharmacy.com');

        $admin = User::firstOrCreate(
            ['email' => $email],
            [
                'name'        => env('ADMIN_NAME', 'System Admin'),
                'password'    => Hash::make(env('ADMIN_PASSWORD', 'Admin@12345')),
                'pharmacy_id' => null, // super-admin has no pharmacy
                'branch_id'   => null, // super-admin has no branch
            ]
        );

        if (!$admin->hasRole('super-admin')) {
            $admin->assignRole('super-admin');
        }

        $this->command->info('✅ Super admin ready');
        $this->command->table(
            ['Field', 'Value'],
            [
                ['Email',    $admin->email],
                ['Password', env('ADMIN_PASSWORD', 'Admin@12345')],
                ['Role',     'super-admin'],
            ]
        );
        $this->command->warn('⚠️  Change the password immediately after first login.');
    }
}
