<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * PharmacyOwnerSeeder
 *
 * Creates a test pharmacy and a pharmacy-owner account linked to it.
 * Requires RoleSeeder to have run first.
 *
 * In production:
 *   - Pharmacy is created via POST /api/v1/pharmacies  (super-admin)
 *   - Owner account is created via POST /api/v1/users  (super-admin)
 *   - This seeder is for development/testing only
 *
 * Run:
 *   php artisan db:seed --class=PharmacyOwnerSeeder
 *
 * Credentials:
 *   Email:    owner@pharmacy.com
 *   Password: Owner@12345
 */
class PharmacyOwnerSeeder extends Seeder
{
    public function run(): void
    {
        // ── Create a test pharmacy ─────────────────────────────────────────
        $pharmacy = Pharmacy::firstOrCreate(
            ['pharmacy_slug' => 'test-pharmacy'],
            [
                'pharmacy_name' => [
                    'en' => 'Test Pharmacy',
                    'ar' => 'صيدلية تجريبية',
                ],
            ]
        );

        $this->command->info("✅ Pharmacy ready: [{$pharmacy->id}] Test Pharmacy");

        // ── Create pharmacy-owner account ─────────────────────────────────
        $owner = User::firstOrCreate(
            ['email' => 'owner@pharmacy.com'],
            [
                'name'        => 'Pharmacy Owner',
                'password'    => Hash::make('Owner@12345'),
                'pharmacy_id' => $pharmacy->id,
                'branch_id'   => null, // owners have no direct branch
            ]
        );

        // Update pharmacy_id if user already existed without it
        if (!$owner->pharmacy_id) {
            $owner->update(['pharmacy_id' => $pharmacy->id]);
        }

        if (!$owner->hasRole('pharmacy-owner')) {
            $owner->assignRole('pharmacy-owner');
        }

        $this->command->info('✅ Pharmacy owner ready');
        $this->command->table(
            ['Field', 'Value'],
            [
                ['Email',       $owner->email],
                ['Password',    'Owner@12345'],
                ['Role',        'pharmacy-owner'],
                ['Pharmacy ID', $pharmacy->id],
                ['Pharmacy',    'Test Pharmacy'],
            ]
        );
    }
}
