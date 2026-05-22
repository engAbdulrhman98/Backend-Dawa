<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    // public function run(): void
    // {
    //     // User::factory(10)->create();


    //     User::factory()->create([
    //         'name' => 'Test User',
    //         'email' => 'test@example.com',
    //     ]);
    //     $this->call(
    //         [
    //             LocationSeeder::class,
    //             CategorySeeder::class,
    //             MedicineSeeder::class,
    //             AdminSeeder::class,
    //             PharmacyAndBranchSeeder::class,
    //         ],
    //     );
    // }

    public function run(): void
    {
        $this->command->info('');
        $this->command->info('🌱 Starting database seeding...');
        $this->command->info('');

        $this->call([

            DummySeeder::class,
            LocationSeeder::class,
            CategorySeeder::class,
            MedicineSeeder::class,
            AuthSeeder::class,          // 1. roles + permissions FIRST
            AdminSeeder::class,
            RoleSeeder::class,          // 1. roles + permissions FIRST
            AdminSeeder::class,         // 2. super-admin account
            PharmacyAndBranchSeeder::class,
            ClientSeeder::class,        // 5. test clients
            PharmacyOwnerSeeder::class, // 3. test pharmacy + owner
           //BranchManagerSeeder::class, // 4. test branch + manager
        ]);

        $this->command->info('');
        $this->command->info('✅ All seeders completed.');
        $this->command->info('');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['super-admin',    'admin@pharmacy.com',   'Admin@12345'],
                ['pharmacy-owner', 'owner@pharmacy.com',   'Owner@12345'],
                ['branch-manager', 'manager@pharmacy.com', 'Manager@12345'],
                ['client',         'client@pharmacy.com',  'Client@12345'],
                ['client',         'client2@pharmacy.com', 'Client@12345'],
            ]
        );
        $this->command->warn('⚠️  Change all passwords before going to production.');
    }
}
