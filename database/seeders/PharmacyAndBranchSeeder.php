<?php

// namespace Database\Seeders;

// use App\Models\Branch;
// use App\Models\City;
// use App\Models\Medicine;
// use App\Models\Pharmacy;
// use Illuminate\Database\Seeder;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Str;

// class PharmacyAndBranchSeeder extends Seeder
// {
//     public function run(): void
//     {
//         // 1. Seed Pharmacies using the Pharmacy Model
//         $pharmacyData = [
//             ['en' => 'Care Pharmacy', 'ar' => 'صيدلية الرعاية'],
//             ['en' => 'Health Hub', 'ar' => 'مركز الصحة'],
//             ['en' => 'Green Cross', 'ar' => 'الصليب الأخضر'],
//         ];

//         foreach ($pharmacyData as $data) {
//             Pharmacy::create([
//                 'pharmacy_name' => $data, // Model cast handles json_encode
//                 'pharmacy_slug' => Str::slug($data['en']),
//             ]);
//         }

//         // 2. Get IDs using Models
//         $city = City::first();

//         if (!$city) {
//             $this->command->error("No cities found! Please run CitySeeder first.");
//             return;
//         }

//         $pharmacyIds = Pharmacy::pluck('id')->toArray();

//         // 3. Seed Branches using the Branch Model
//         $branches = [
//             [
//                 'name' => ['en' => 'Downtown Branch', 'ar' => 'فرع وسط المدينة'],
//                 'address' => ['en' => '123 Main St, Cairo', 'ar' => '١٢٣ شارع رئيسي، القاهرة'],
//                 'phone' => ['en' => '+20123456789', 'ar' => '+٢٠١٢٣٤٥٦٧٨٩'],
//                 'lat' => 30.0444,
//                 'lng' => 31.2357,
//             ],
//             [
//                 'name' => ['en' => 'Giza Heights', 'ar' => 'هضبة الجيزة'],
//                 'address' => ['en' => '456 Pyramid Road, Giza', 'ar' => '٤٥٦ طريق الهرم، الجيزة'],
//                 'phone' => ['en' => '+20987654321', 'ar' => '+٢٠٩٨٧٦٥٤٣٢١'],
//                 'lat' => 29.9773,
//                 'lng' => 31.1325,
//             ],
//         ];

//         foreach ($branches as $index => $branch) {
//             Branch::create([
//                 'city_id'        => $city->id,
//                 'pharmacy_id'    => $pharmacyIds[$index % count($pharmacyIds)],
//                 'branch_name'    => $branch['name'],
//                 'branch_address' => $branch['address'],
//                 'branch_phone'   => $branch['phone'],
//                 'latitude'       => $branch['lat'],
//                 'longitude'      => $branch['lng'],
//                 'branch_slug'    => Str::slug($branch['name']['en']),
//             ]);
//         }

//         // 4. Seed Pivot Table (branch_medicine)
//         $branchIds = Branch::pluck('id')->toArray();
//         $medicineIds = Medicine::pluck('id')->toArray();

//         if (empty($branchIds) || empty($medicineIds)) {
//             $this->command->warn("Skipping BranchMedicine: Branches or Medicines missing.");
//             return;
//         }

//         foreach ($branchIds as $branchId) {
//             $branch = Branch::find($branchId);
//             $howMany = rand(1, min(5, count($medicineIds)));
//             $randomKeys = (array) array_rand($medicineIds, $howMany);

//             foreach ($randomKeys as $key) {
//                 // Using the relationship defined in the Branch model
//                 $branch->medicines()->attach($medicineIds[$key], [
//                     'quantity' => rand(0, 100)
//                 ]);
//             }
//         }
//     }
// }
namespace Database\Seeders;

use App\Models\Branch;
use App\Models\City;
use App\Models\Medicine;
use App\Models\Pharmacy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PharmacyAndBranchSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Pharmacies using the Pharmacy Model (Real Egyptian Pharmacy Chains)
        $pharmacyData = [
            ['en' => 'El Ezaby Pharmacy', 'ar' => 'صيدليات العزبي'],
            ['en' => 'Seif Pharmacies', 'ar' => 'صيدليات سيف'],
            ['en' => 'Misr Pharmacies', 'ar' => 'صيدليات مصر'],
            ['en' => 'Delmar & Attalla', 'ar' => 'صيدليات دلمار وعطالله'],
            ['en' => 'Ali & Ali Pharmacies', 'ar' => 'صيدليات علي وعلي'],
            ['en' => 'Care Pharmacies', 'ar' => 'صيدليات كير'],
            ['en' => 'Roshdy Pharmacies', 'ar' => 'صيدليات رشدي'],
            ['en' => 'Fouda Pharmacies', 'ar' => 'صيدليات فودة'],
        ];

        foreach ($pharmacyData as $data) {
            Pharmacy::create([
                'pharmacy_name' => $data, // Model cast handles json_encode
                'pharmacy_slug' => Str::slug($data['en']),
            ]);
        }

        // 2. Get IDs using Models
        // ملاحظة: أنت هنا تجلب أول مدينة فقط، تأكد أن هذا يتماشى مع منطق قاعدة بياناتك
        // إذا كانت الفروع في مدن مختلفة (القاهرة، الجيزة) قد تحتاج لجلب المدن بشكل ديناميكي
        $city = City::first();

        if (!$city) {
            $this->command->error("No cities found! Please run CitySeeder first.");
            return;
        }

        $pharmacyIds = Pharmacy::pluck('id')->toArray();

        // 3. Seed Branches using the Branch Model
        $branches = [
            [
                'name' => ['en' => 'Downtown Branch', 'ar' => 'فرع وسط البلد'],
                'address' => ['en' => 'Talaat Harb St, Downtown, Cairo', 'ar' => 'شارع طلعت حرب، وسط البلد، القاهرة'],
                'phone' => ['en' => '19600', 'ar' => '19600'],
                'lat' => 30.0444,
                'lng' => 31.2357,
            ],
            [
                'name' => ['en' => 'Haram Branch', 'ar' => 'فرع الهرم'],
                'address' => ['en' => 'Al Haram St, Giza', 'ar' => 'شارع الهرم الرئيسي، الجيزة'],
                'phone' => ['en' => '19110', 'ar' => '19110'],
                'lat' => 29.9773,
                'lng' => 31.1325,
            ],
            [
                'name' => ['en' => 'Maadi Branch', 'ar' => 'فرع المعادي'],
                'address' => ['en' => 'Road 9, Maadi, Cairo', 'ar' => 'شارع ٩، المعادي، القاهرة'],
                'phone' => ['en' => '19044', 'ar' => '19044'],
                'lat' => 29.9602,
                'lng' => 31.2569,
            ],
            [
                'name' => ['en' => 'Korba Branch', 'ar' => 'فرع الكوربة'],
                'address' => ['en' => 'Korba Square, Heliopolis, Cairo', 'ar' => 'ميدان الكوربة، مصر الجديدة، القاهرة'],
                'phone' => ['en' => '19955', 'ar' => '19955'],
                'lat' => 30.1042,
                'lng' => 31.3283,
            ],
            [
                'name' => ['en' => 'Nasr City Branch', 'ar' => 'فرع مدينة نصر'],
                'address' => ['en' => 'Abbas El Akkad St, Nasr City, Cairo', 'ar' => 'شارع عباس العقاد، مدينة نصر، القاهرة'],
                'phone' => ['en' => '19800', 'ar' => '19800'],
                'lat' => 30.0626,
                'lng' => 31.3242,
            ],
            [
                'name' => ['en' => 'Zamalek Branch', 'ar' => 'فرع الزمالك'],
                'address' => ['en' => '26th of July Corridor, Zamalek, Cairo', 'ar' => 'محور ٢٦ يوليو، الزمالك، القاهرة'],
                'phone' => ['en' => '19700', 'ar' => '19700'],
                'lat' => 30.0626,
                'lng' => 31.2210,
            ],
            [
                'name' => ['en' => '5th Settlement Branch', 'ar' => 'فرع التجمع الخامس'],
                'address' => ['en' => 'South 90th St, New Cairo', 'ar' => 'شارع التسعين الجنوبي، القاهرة الجديدة'],
                'phone' => ['en' => '19200', 'ar' => '19200'],
                'lat' => 30.0276,
                'lng' => 31.4811,
            ],
            [
                'name' => ['en' => 'Sheikh Zayed Branch', 'ar' => 'فرع الشيخ زايد'],
                'address' => ['en' => 'Arkan Plaza, Sheikh Zayed, Giza', 'ar' => 'أركان بلازا، الشيخ زايد، الجيزة'],
                'phone' => ['en' => '19300', 'ar' => '19300'],
                'lat' => 30.0450,
                'lng' => 30.9856,
            ],
        ];

        foreach ($branches as $index => $branch) {
            Branch::create([
                'city_id'        => $city->id,
                'pharmacy_id'    => $pharmacyIds[$index % count($pharmacyIds)], // Distributes branches evenly across available pharmacies
                'branch_name'    => $branch['name'],
                'branch_address' => $branch['address'],
                'branch_phone'   => $branch['phone'],
                'latitude'       => $branch['lat'],
                'longitude'      => $branch['lng'],
                'branch_slug'    => Str::slug($branch['name']['en'] . '-' . $index), // Added index to ensure slug uniqueness
            ]);
        }

        // 4. Seed Pivot Table (branch_medicine)
        $branchIds = Branch::pluck('id')->toArray();
        $medicineIds = Medicine::pluck('id')->toArray();

        if (empty($branchIds) || empty($medicineIds)) {
            $this->command->warn("Skipping BranchMedicine: Branches or Medicines missing.");
            return;
        }

        foreach ($branchIds as $branchId) {
            $branch = Branch::find($branchId);
            $howMany = rand(1, min(10, count($medicineIds))); // Increased max medicines per branch
            $randomKeys = (array) array_rand($medicineIds, $howMany);

            foreach ($randomKeys as $key) {
                // Using the relationship defined in the Branch model
                $branch->medicines()->attach($medicineIds[$key], [
                    'quantity' => rand(0, 500) // Increased stock variance
                ]);
            }
        }
    }
}