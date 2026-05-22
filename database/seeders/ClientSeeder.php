<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * ClientSeeder
 *
 * Creates test client / customer accounts.
 * Requires RoleSeeder to have run first.
 *
 * In production:
 *   - Clients register themselves via POST /api/v1/auth/register
 *   - This seeder is for development/testing only
 *
 * Run:
 *   php artisan db:seed --class=ClientSeeder
 *
 * Test accounts:
 *   client@pharmacy.com  / Client@12345
 *   client2@pharmacy.com / Client@12345
 */
class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $clients = [
            [
                'name'  => 'Test Client',
                'email' => 'client@pharmacy.com',
            ],
            [
                'name'  => 'Test Client 2',
                'email' => 'client2@pharmacy.com',
            ],
        ];

        foreach ($clients as $data) {
            $client = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'        => $data['name'],
                    'password'    => Hash::make('Client@12345'),
                    'pharmacy_id' => null, // clients have no pharmacy
                    'branch_id'   => null, // clients have no branch
                ]
            );

            if (!$client->hasRole('client')) {
                $client->assignRole('client');
            }
        }

        $this->command->info('✅ Clients ready');
        $this->command->table(
            ['Email', 'Password', 'Role'],
            array_map(fn($c) => [$c['email'], 'Client@12345', 'client'], $clients)
        );
        $this->command->line('  → Clients can self-register via POST /api/v1/auth/register');
    }
}
