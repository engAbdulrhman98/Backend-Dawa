<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeographyTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_countries()
    {
        Country::create([
            'country_name' => ['en' => 'Egypt', 'ar' => 'مصر'],
        ]);

        $response = $this->getJson('/api/v1/countries');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.name', 'Egypt');
    }

    public function test_can_fetch_governorates()
    {
        $country = Country::create([
            'country_name' => ['en' => 'Egypt', 'ar' => 'مصر'],
        ]);

        Governorate::create([
            'country_id' => $country->id,
            'governorate_name' => ['en' => 'Cairo', 'ar' => 'القاهرة'],
        ]);

        // Test fetching all governorates
        $response1 = $this->getJson('/api/v1/governorates');
        $response1->assertStatus(200)
                  ->assertJsonCount(1, 'data')
                  ->assertJsonPath('data.0.name', 'Cairo');

        // Test fetching governorates for specific country
        $response2 = $this->getJson("/api/v1/countries/{$country->country_slug}/governorates");
        $response2->assertStatus(200)
                  ->assertJsonCount(1, 'data');
    }

    public function test_can_fetch_cities()
    {
        $country = Country::create([
            'country_name' => ['en' => 'Egypt', 'ar' => 'مصر'],
        ]);

        $gov = Governorate::create([
            'country_id' => $country->id,
            'governorate_name' => ['en' => 'Cairo', 'ar' => 'القاهرة'],
        ]);

        City::create([
            'governorate_id' => $gov->id,
            'city_name' => ['en' => 'Nasr City', 'ar' => 'مدينة نصر'],
        ]);

        // Test fetching all cities
        $response1 = $this->getJson('/api/v1/cities');
        $response1->assertStatus(200)
                  ->assertJsonCount(1, 'data')
                  ->assertJsonPath('data.0.name', 'Nasr City');

        // Test fetching cities for specific governorate
        $response2 = $this->getJson("/api/v1/governorates/{$gov->governorate_slug}/cities");
        $response2->assertStatus(200)
                  ->assertJsonCount(1, 'data');
    }
}
