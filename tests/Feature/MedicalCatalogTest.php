<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Medicine;
use App\Models\Branch;
use App\Models\Pharmacy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_categories()
    {
        Category::create([
            'category_name' => ['en' => 'Analgesics', 'ar' => 'مسكنات'],
            'category_description' => ['en' => 'Painkillers', 'ar' => 'مسكنات للألم'],
        ]);

        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.name', 'Analgesics');
    }

    public function test_can_fetch_medicines()
    {
        $category = Category::create([
            'category_name' => ['en' => 'Analgesics', 'ar' => 'مسكنات'],
            'category_description' => ['en' => 'Painkillers', 'ar' => 'مسكنات للألم'],
        ]);

        $medicine = Medicine::create([
            'category_id' => $category->id,
            'medicine_name' => ['en' => 'Paracetamol', 'ar' => 'باراسيتامول'],
            'medicine_description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'medicine_usage' => ['en' => 'Usage', 'ar' => 'استخدام'],
            'medicine_side_effects' => ['en' => 'None', 'ar' => 'لا يوجد'],
            'medicine_price' => 10.50,
        ]);

        $response = $this->getJson('/api/v1/medicines');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.medicine_name.en', 'Paracetamol');

        $responseShow = $this->getJson("/api/v1/medicines/{$medicine->medicine_slug}");
        $responseShow->assertStatus(200)
                     ->assertJsonPath('data.medicine_name.en', 'Paracetamol');
    }

    public function test_can_search_medicines()
    {
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

        Medicine::create([
            'category_id' => $category->id,
            'medicine_name' => ['en' => 'Aspirin', 'ar' => 'أسبرين'],
            'medicine_description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'medicine_usage' => ['en' => 'Usage', 'ar' => 'استخدام'],
            'medicine_side_effects' => ['en' => 'None', 'ar' => 'لا يوجد'],
            'medicine_price' => 15.00,
        ]);

        // Search for Paracetamol
        $response = $this->getJson('/api/v1/medicines/search?q=Para');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.medicine_name.en', 'Paracetamol');
    }

    public function test_can_fetch_medicines_by_category()
    {
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

        $response = $this->getJson("/api/v1/categories/{$category->category_slug}/medicines");

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.medicine_name.en', 'Paracetamol');
    }
    public function test_can_upload_and_rename_medicine_image()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        
        \Spatie\Permission\Models\Role::create(['name' => 'super-admin']);
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin, 'api');

        $category = Category::create([
            'category_name' => ['en' => 'Analgesics', 'ar' => 'مسكنات'],
            'category_description' => ['en' => 'Painkillers', 'ar' => 'مسكنات للألم'],
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->create('my-custom-photo.jpg', 100);

        $response = $this->postJson("/api/v1/categories/{$category->category_slug}/medicines", [
            'category_id' => $category->id,
            'medicine_name' => ['en' => 'Aspirin', 'ar' => 'أسبرين'],
            'medicine_description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'medicine_usage' => ['en' => 'Usage', 'ar' => 'استخدام'],
            'medicine_side_effects' => ['en' => 'None', 'ar' => 'لا يوجد'],
            'medicine_price' => 15.00,
            'image' => $file,
        ]);

        $response->assertStatus(201)
                 ->assertJsonPath('data.medicine_name.en', 'Aspirin');
                 
        $medicine = Medicine::where('medicine_slug', 'aspirin')->first();
        $this->assertNotNull($medicine);
        
        $media = $medicine->getFirstMedia('medicines');
        $this->assertNotNull($media);
        
        // Assert that the file was renamed to slug.extension
        $this->assertEquals('aspirin.jpg', $media->file_name);
    }

    public function test_can_update_medicine_without_changing_image()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        
        \Spatie\Permission\Models\Role::create(['name' => 'super-admin']);
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin, 'api');

        $category = Category::create([
            'category_name' => ['en' => 'Analgesics', 'ar' => 'مسكنات'],
            'category_description' => ['en' => 'Painkillers', 'ar' => 'مسكنات للألم'],
        ]);

        $medicine = Medicine::create([
            'category_id' => $category->id,
            'medicine_name' => ['en' => 'Panadol', 'ar' => 'بنادول'],
            'medicine_description' => ['en' => 'Pain reliever', 'ar' => 'مسكن ألم'],
            'medicine_price' => 50.00,
        ]);

        // Add a mock file to represent existing image
        $file = \Illuminate\Http\UploadedFile::fake()->create('existing-photo.jpg', 100);
        $medicine->addMedia($file)->toMediaCollection('medicines');
        
        $this->assertNotNull($medicine->getFirstMedia('medicines'));
        $existingImageUrl = $medicine->getFirstMediaUrl('medicines');

        // Update medicine name and price, sending the image as its string URL (existing image not changed)
        $response = $this->putJson("/api/v1/medicines/{$medicine->medicine_slug}", [
            'medicine_name' => ['en' => 'Panadol Extra', 'ar' => 'بنادول إكسترا'],
            'medicine_price' => 60.00,
            'image' => $existingImageUrl, // Passed as a string URL
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('data.medicine_name.en', 'Panadol Extra')
                 ->assertJsonPath('data.price', '60.00');

        // Verify the original image remains completely unchanged
        $medicine->refresh();
        $this->assertNotNull($medicine->getFirstMedia('medicines'));
    }

    public function test_can_update_medicine_with_new_image()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        
        \Spatie\Permission\Models\Role::create(['name' => 'super-admin']);
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin, 'api');

        $category = Category::create([
            'category_name' => ['en' => 'Analgesics', 'ar' => 'مسكنات'],
            'category_description' => ['en' => 'Painkillers', 'ar' => 'مسكنات للألم'],
        ]);

        $medicine = Medicine::create([
            'category_id' => $category->id,
            'medicine_name' => ['en' => 'Panadol', 'ar' => 'بنادول'],
            'medicine_price' => 50.00,
        ]);

        $oldFile = \Illuminate\Http\UploadedFile::fake()->create('old-photo.jpg', 100);
        $medicine->addMedia($oldFile)->toMediaCollection('medicines');
        $oldMediaId = $medicine->getFirstMedia('medicines')->id;

        $newFile = \Illuminate\Http\UploadedFile::fake()->create('new-photo.png', 150);

        // Send request with PUT trick using POST and _method=PUT to simulate multipart/form-data upload
        $response = $this->postJson("/api/v1/medicines/{$medicine->medicine_slug}", [
            '_method' => 'PUT',
            'medicine_name' => ['en' => 'Panadol Advanced', 'ar' => 'بنادول متطور'],
            'medicine_price' => 70.00,
            'image' => $newFile, // Uploading a new file
        ]);

        $response->assertStatus(200);

        $medicine->refresh();
        $newMedia = $medicine->getFirstMedia('medicines');
        $this->assertNotNull($newMedia);
        $this->assertNotEquals($oldMediaId, $newMedia->id);
        $this->assertEquals('panadol.png', $newMedia->file_name); // Named after the slug
    }

    public function test_can_create_medicine_associated_with_branches()
    {
        \Spatie\Permission\Models\Role::create(['name' => 'super-admin']);
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin, 'api');

        $country = \App\Models\Country::create(['country_name' => ['en' => 'Egypt', 'ar' => 'مصر']]);
        $gov = \App\Models\Governorate::create(['country_id' => $country->id, 'governorate_name' => ['en' => 'Cairo', 'ar' => 'القاهرة']]);
        $city = \App\Models\City::create(['governorate_id' => $gov->id, 'city_name' => ['en' => 'Nasr City', 'ar' => 'مدينة نصر']]);

        $category = Category::create([
            'category_name' => ['en' => 'Analgesics', 'ar' => 'مسكنات'],
            'category_description' => ['en' => 'Painkillers', 'ar' => 'مسكنات للألم'],
        ]);

        $pharmacy = Pharmacy::create([
            'pharmacy_name' => ['en' => 'El Ezaby', 'ar' => 'العزبي']
        ]);

        $branch = Branch::create([
            'pharmacy_id' => $pharmacy->id,
            'city_id' => $city->id,
            'branch_name' => ['en' => 'Downtown', 'ar' => 'وسط البلد'],
            'branch_address' => ['en' => 'Downtown St', 'ar' => 'شارع وسط البلد'],
            'branch_phone' => ['en' => '0123456789', 'ar' => '0123456789'],
            'latitude' => 30.0444,
            'longitude' => 31.2357,
        ]);

        $response = $this->postJson("/api/v1/categories/{$category->category_slug}/medicines", [
            'category_id' => $category->id,
            'medicine_name' => ['en' => 'Panadol Extra', 'ar' => 'بنادول اكسترا'],
            'medicine_price' => 50.00,
            'branches' => [$branch->id],
        ]);

        $response->assertStatus(201);
        
        $medicine = Medicine::where('medicine_slug', 'panadol-extra')->first();
        $this->assertNotNull($medicine);
        $this->assertCount(1, $medicine->branches);
        $this->assertEquals($branch->id, $medicine->branches->first()->id);
    }

    public function test_can_update_medicine_associated_with_branches()
    {
        \Spatie\Permission\Models\Role::create(['name' => 'super-admin']);
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole('super-admin');
        $this->actingAs($admin, 'api');

        $country = \App\Models\Country::create(['country_name' => ['en' => 'Egypt', 'ar' => 'مصر']]);
        $gov = \App\Models\Governorate::create(['country_id' => $country->id, 'governorate_name' => ['en' => 'Cairo', 'ar' => 'القاهرة']]);
        $city = \App\Models\City::create(['governorate_id' => $gov->id, 'city_name' => ['en' => 'Nasr City', 'ar' => 'مدينة نصر']]);

        $category = Category::create([
            'category_name' => ['en' => 'Analgesics', 'ar' => 'مسكنات'],
            'category_description' => ['en' => 'Painkillers', 'ar' => 'مسكنات للألم'],
        ]);

        $medicine = Medicine::create([
            'category_id' => $category->id,
            'medicine_name' => ['en' => 'Panadol', 'ar' => 'بنادول'],
            'medicine_price' => 45.00,
        ]);

        $pharmacy = Pharmacy::create([
            'pharmacy_name' => ['en' => 'El Ezaby', 'ar' => 'العزبي']
        ]);

        $branch = Branch::create([
            'pharmacy_id' => $pharmacy->id,
            'city_id' => $city->id,
            'branch_name' => ['en' => 'Downtown', 'ar' => 'وسط البلد'],
            'branch_address' => ['en' => 'Downtown St', 'ar' => 'شارع وسط البلد'],
            'branch_phone' => ['en' => '0123456789', 'ar' => '0123456789'],
            'latitude' => 30.0444,
            'longitude' => 31.2357,
        ]);

        $response = $this->putJson("/api/v1/medicines/{$medicine->medicine_slug}", [
            'medicine_name' => ['en' => 'Panadol Advanced', 'ar' => 'بنادول متطور'],
            'medicine_price' => 55.00,
            'branches' => [$branch->id],
        ]);

        $response->assertStatus(200);

        $medicine->refresh();
        $this->assertCount(1, $medicine->branches);
        $this->assertEquals($branch->id, $medicine->branches->first()->id);
    }
}
