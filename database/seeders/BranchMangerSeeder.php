<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Branch;
use App\Models\City;
use App\Models\Pharmacy;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * BranchManagerSeeder
 *
 * Creates a test branch and a branch-manager account linked to it.
 * Depends on PharmacyOwnerSeeder (needs the test pharmacy).
 * Requires RoleSeeder to have run first.
 *
 * In production:
 *   - Branch is created via POST /api/v1/branches        (super-admin | owner)
 *   - Manager account is created via POST /api/v1/users  (super-admin | owner)
 *   - This seeder is for development/testing only
 *
 * Run:
 *   php artisan db:seed --class=BranchManagerSeeder
 *
 * Credentials:
 *   Email:    manager@pharmacy.com
 *   Password: Manager@12345
 */
class BranchManagerSeeder extends Seeder
{
    public function run(): void
    {
        // ── Resolve the test pharmacy (created by PharmacyOwnerSeeder) ─────
        $pharmacy = Pharmacy::where('pharmacy_slug', 'test-pharmacy')->first();

        if (!$pharmacy) {
            $this->command->error('❌ Test pharmacy not found. Run PharmacyOwnerSeeder first.');
            return;
        }

        // ── Resolve a city (use any available city, or skip coordinates) ───
        $city = City::first();

        if (!$city) {
            $this->command->warn('⚠️  No city found. Run a geography seeder or add a city manually.');
            $this->command->warn('    Branch will be created without a city_id — add one later.');
        }

        // ── Create a test branch ───────────────────────────────────────────
        $branch = Branch::firstOrCreate(
            ['branch_slug' => 'test-pharmacy-downtown'],
            [
                'pharmacy_id'  => $pharmacy->id,
                'city_id'      => $city?->id ?? 1,
                'branch_name'  => [
                    'en' => 'Test Pharmacy - Downtown',
                    'ar' => 'صيدلية تجريبية - وسط المدينة',
                ],
                'branch_address' => [
                    'en' => '123 Downtown Street, Cairo',
                    'ar' => '١٢٣ شارع وسط المدينة، القاهرة',
                ],
                'branch_phone' => [
                    'en' => '+20 2 1234 5678',
                    'ar' => '+20 2 1234 5678',
                ],
                // Coordinates will be auto-filled by spatie/geocoder via Branch::booted()
                // when branch_address is set and coordinates are missing
                'latitude'  => null,
                'longitude' => null,
            ]
        );

        $this->command->info("✅ Branch ready: [{$branch->id}] Test Pharmacy - Downtown");

        // ── Create branch-manager account ──────────────────────────────────
        $manager = User::firstOrCreate(
            ['email' => 'manager@pharmacy.com'],
            [
                'name'        => 'Branch Manager',
                'password'    => Hash::make('Manager@12345'),
                'pharmacy_id' => $pharmacy->id,
                'branch_id'   => $branch->id,
            ]
        );

        // Update FKs if user already existed without them
        $needsUpdate = false;
        if (!$manager->pharmacy_id) {
            $manager->pharmacy_id = $pharmacy->id;
            $needsUpdate = true;
        }
        if (!$manager->branch_id) {
            $manager->branch_id   = $branch->id;
            $needsUpdate = true;
        }
        if ($needsUpdate) $manager->save();

        if (!$manager->hasRole('branch-manager')) {
            $manager->assignRole('branch-manager');
        }

        $this->command->info('✅ Branch manager ready');
        $this->command->table(
            ['Field', 'Value'],
            [
                ['Email',       $manager->email],
                ['Password',    'Manager@12345'],
                ['Role',        'branch-manager'],
                ['Pharmacy ID', $pharmacy->id],
                ['Branch ID',   $branch->id],
                ['Branch',      'Test Pharmacy - Downtown'],
            ]
        );
    }
}
