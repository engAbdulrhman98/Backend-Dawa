<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\Pharmacy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PharmacyDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    protected $city;

    protected function setUp(): void
    {
        parent::setUp();

        $country = Country::create(['country_name' => ['en' => 'Egypt', 'ar' => 'مصر']]);
        $gov = Governorate::create(['country_id' => $country->id, 'governorate_name' => ['en' => 'Cairo', 'ar' => 'القاهرة']]);
        $this->city = City::create(['governorate_id' => $gov->id, 'city_name' => ['en' => 'Nasr City', 'ar' => 'مدينة نصر']]);
    }

    public function test_can_fetch_pharmacies()
    {
        $pharmacy = Pharmacy::create([
            'pharmacy_name' => ['en' => 'El Ezaby', 'ar' => 'العزبي']
        ]);

        $response = $this->getJson('/api/v1/pharmacies');
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.name.en', 'El Ezaby');

        $responseShow = $this->getJson("/api/v1/pharmacies/{$pharmacy->pharmacy_slug}");
        $responseShow->assertStatus(200)
                     ->assertJsonPath('data.name.en', 'El Ezaby');
    }

    public function test_can_search_pharmacies()
    {
        Pharmacy::create([
            'pharmacy_name' => ['en' => 'El Ezaby', 'ar' => 'العزبي']
        ]);
        Pharmacy::create([
            'pharmacy_name' => ['en' => 'Seif', 'ar' => 'سيف']
        ]);

        $response = $this->getJson('/api/v1/pharmacies/search?q=Ezaby');
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.name.en', 'El Ezaby');
    }

    public function test_can_fetch_branches()
    {
        $pharmacy = Pharmacy::create([
            'pharmacy_name' => ['en' => 'El Ezaby', 'ar' => 'العزبي']
        ]);

        $branch = Branch::create([
            'pharmacy_id' => $pharmacy->id,
            'city_id' => $this->city->id,
            'branch_name' => ['en' => 'Makram Ebeid', 'ar' => 'مكرم عبيد'],
            'branch_address' => ['en' => 'Makram Ebeid St', 'ar' => 'شارع مكرم عبيد'],
            'branch_phone' => ['en' => '0123456789', 'ar' => '0123456789'],
            'latitude' => 30.0444,
            'longitude' => 31.2357,
        ]);

        $response = $this->getJson('/api/v1/branches');
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.name.en', 'Makram Ebeid');

        $responseShow = $this->getJson("/api/v1/branches/{$branch->branch_slug}");
        $responseShow->assertStatus(200)
                     ->assertJsonPath('data.name.en', 'Makram Ebeid');
    }

    public function test_can_fetch_branches_by_pharmacy()
    {
        $pharmacy = Pharmacy::create([
            'pharmacy_name' => ['en' => 'El Ezaby', 'ar' => 'العزبي']
        ]);

        Branch::create([
            'pharmacy_id' => $pharmacy->id,
            'city_id' => $this->city->id,
            'branch_name' => ['en' => 'Makram Ebeid', 'ar' => 'مكرم عبيد'],
            'branch_address' => ['en' => 'Makram Ebeid St', 'ar' => 'شارع مكرم عبيد'],
            'branch_phone' => ['en' => '0123456789', 'ar' => '0123456789'],
            'latitude' => 30.0444,
            'longitude' => 31.2357,
        ]);

        $response = $this->getJson("/api/v1/pharmacies/{$pharmacy->pharmacy_slug}/branches");
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.name.en', 'Makram Ebeid');
    }

    public function test_can_find_nearby_branches()
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            $this->markTestSkipped('SQLite does not support math functions needed for nearby query.');
        }

        $pharmacy = Pharmacy::create([
            'pharmacy_name' => ['en' => 'El Ezaby', 'ar' => 'العزبي']
        ]);

        Branch::create([
            'pharmacy_id' => $pharmacy->id,
            'city_id' => $this->city->id,
            'branch_name' => ['en' => 'Nasr City', 'ar' => 'مدينة نصر'],
            'branch_address' => ['en' => 'Nasr City St', 'ar' => 'شارع مدينة نصر'],
            'branch_phone' => ['en' => '0123456789', 'ar' => '0123456789'],
            'latitude' => 30.0444,
            'longitude' => 31.2357, // near Cairo
        ]);

        Branch::create([
            'pharmacy_id' => $pharmacy->id,
            'city_id' => $this->city->id,
            'branch_name' => ['en' => 'Alexandria', 'ar' => 'الاسكندرية'],
            'branch_address' => ['en' => 'Alex St', 'ar' => 'شارع الاسكندرية'],
            'branch_phone' => ['en' => '0123456789', 'ar' => '0123456789'],
            'latitude' => 31.2001,
            'longitude' => 29.9187, // far from Cairo
        ]);

        $response = $this->getJson('/api/v1/branches/nearby?lat=30.0444&lng=31.2357&radius=10');
        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.name.en', 'Nasr City');
    }
}
