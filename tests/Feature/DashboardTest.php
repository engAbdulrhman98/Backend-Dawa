<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Pharmacy;
use App\Models\Branch;
use App\Models\Medicine;
use App\Models\Category;
use App\Models\Country;
use App\Models\Governorate;
use App\Models\City;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected $city;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'pharmacy-owner', 'guard_name' => 'web']);

        $country = Country::create(['country_name' => ['en' => 'Egypt', 'ar' => 'مصر']]);
        $gov = Governorate::create(['country_id' => $country->id, 'governorate_name' => ['en' => 'Cairo', 'ar' => 'القاهرة']]);
        $this->city = City::create(['governorate_id' => $gov->id, 'city_name' => ['en' => 'Nasr City', 'ar' => 'مدينة نصر']]);
    }

    public function test_super_admin_can_fetch_dashboard_stats()
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        // Create sample data manually
        $pharmacy1 = Pharmacy::create(['pharmacy_name' => ['en' => 'Ezaby', 'ar' => 'العزبي']]);
        $pharmacy2 = Pharmacy::create(['pharmacy_name' => ['en' => 'Seif', 'ar' => 'سيف']]);

        Branch::create([
            'pharmacy_id' => $pharmacy1->id,
            'city_id' => $this->city->id,
            'branch_name' => ['en' => 'Downtown', 'ar' => 'وسط البلد'],
            'branch_address' => ['en' => 'Downtown St', 'ar' => 'شارع وسط البلد'],
            'branch_phone' => ['en' => '0123456789', 'ar' => '0123456789'],
            'latitude' => 30.0444,
            'longitude' => 31.2357,
        ]);

        $category = Category::create([
            'category_name' => ['en' => 'Analgesics', 'ar' => 'مسكنات'],
            'category_description' => ['en' => 'Painkillers', 'ar' => 'مسكنات للألم'],
        ]);

        Medicine::create([
            'category_id' => $category->id,
            'medicine_name' => ['en' => 'Paracetamol', 'ar' => 'باراسيتامول'],
            'medicine_description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'medicine_usage' => ['en' => 'Usage', 'ar' => 'استخدام'],
            'medicine_side_effects' => ['en' => 'None', 'ar' => 'لا يوجد'],
            'medicine_price' => 10.50,
        ]);

        $response = $this->actingAs($admin, 'api')->getJson('/api/v1/dashboard/stats');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'data' => [
                         'medicines_count'  => 1,
                         'pharmacies_count' => 2,
                         'branches_count'   => 1,
                     ]
                 ]);
    }

    public function test_pharmacy_owner_can_fetch_dashboard_stats()
    {
        // Create pharmacy and owner
        $pharmacy = Pharmacy::create(['pharmacy_name' => ['en' => 'Ezaby', 'ar' => 'العزبي']]);
        $owner = User::factory()->create(['pharmacy_id' => $pharmacy->id]);
        $owner->assignRole('pharmacy-owner');

        // Create branches for this pharmacy
        $branch1 = Branch::create([
            'pharmacy_id' => $pharmacy->id,
            'city_id' => $this->city->id,
            'branch_name' => ['en' => 'Downtown', 'ar' => 'وسط البلد'],
            'branch_address' => ['en' => 'Downtown St', 'ar' => 'شارع وسط البلد'],
            'branch_phone' => ['en' => '0123456789', 'ar' => '0123456789'],
            'latitude' => 30.0444,
            'longitude' => 31.2357,
        ]);
        $branch2 = Branch::create([
            'pharmacy_id' => $pharmacy->id,
            'city_id' => $this->city->id,
            'branch_name' => ['en' => 'Nasr City Branch', 'ar' => 'فرع مدينة نصر'],
            'branch_address' => ['en' => 'Nasr City St', 'ar' => 'شارع مدينة نصر'],
            'branch_phone' => ['en' => '0123456789', 'ar' => '0123456789'],
            'latitude' => 30.0450,
            'longitude' => 31.2360,
        ]);

        // Create an unrelated branch
        $otherPharmacy = Pharmacy::create(['pharmacy_name' => ['en' => 'Seif', 'ar' => 'سيف']]);
        Branch::create([
            'pharmacy_id' => $otherPharmacy->id,
            'city_id' => $this->city->id,
            'branch_name' => ['en' => 'Other Branch', 'ar' => 'فرع آخر'],
            'branch_address' => ['en' => 'Other St', 'ar' => 'شارع آخر'],
            'branch_phone' => ['en' => '0123456789', 'ar' => '0123456789'],
            'latitude' => 30.0500,
            'longitude' => 31.2400,
        ]);

        // Create medicines
        $category = Category::create([
            'category_name' => ['en' => 'Analgesics', 'ar' => 'مسكنات'],
            'category_description' => ['en' => 'Painkillers', 'ar' => 'مسكنات للألم'],
        ]);

        $medicine1 = Medicine::create([
            'category_id' => $category->id,
            'medicine_name' => ['en' => 'Paracetamol', 'ar' => 'باراسيتامول'],
            'medicine_description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'medicine_usage' => ['en' => 'Usage', 'ar' => 'استخدام'],
            'medicine_side_effects' => ['en' => 'None', 'ar' => 'لا يوجد'],
            'medicine_price' => 10.50,
        ]);
        $medicine2 = Medicine::create([
            'category_id' => $category->id,
            'medicine_name' => ['en' => 'Aspirin', 'ar' => 'أسبرين'],
            'medicine_description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'medicine_usage' => ['en' => 'Usage', 'ar' => 'استخدام'],
            'medicine_side_effects' => ['en' => 'None', 'ar' => 'لا يوجد'],
            'medicine_price' => 15.00,
        ]);

        // Associate medicines with the pharmacy's branches
        $branch1->medicines()->attach($medicine1->id, ['quantity' => 20]);
        $branch2->medicines()->attach($medicine2->id, ['quantity' => 15]);

        $response = $this->actingAs($owner, 'api')->getJson('/api/v1/dashboard/stats');

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => 'success',
                     'data' => [
                         'pharmacies_count' => 1,
                         'branches_count'   => 2,
                         'medicines_count'  => 2,
                     ]
                 ]);
    }
}
