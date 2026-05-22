<?php

namespace Database\Seeders;

use App\Models\Dummy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $name = "Test Dummy {$i}";

            Dummy::create([
                'dummy_name' => $name,
                'dummy_description' => "Description for dummy {$i}",
                'dummy_image' => "dummy{$i}.jpg",
                'dummy_translations' => [
                    'en' => $name,
                    'ar' => "اختبار {$i}"
                ],
                'dummy_slug' => Str::slug($name),
            ]);
        }
    }
}
