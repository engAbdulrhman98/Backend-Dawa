<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //     /*
    //     |--------------------------------------------------------------------------
    //     | Helper Function
    //     |--------------------------------------------------------------------------
    //     */
    //     $createData = function ($countrySlug, $countryName, $data) {

    //         $country = Country::firstOrCreate(
    //             ['country_slug' => $countrySlug],
    //             ['country_name' => $countryName]
    //         );

    //         foreach ($data as $slug => $govData) {

    //             $gov = Governorate::firstOrCreate(
    //                 [
    //                     'governorate_slug' => $slug,
    //                     'country_id' => $country->id
    //                 ],
    //                 [
    //                     'governorate_name' => $govData['name'],
    //                 ]
    //             );

    //             foreach ($govData['cities'] as $city) {

    //                 $citySlug = strtolower(str_replace(' ', '-', $city['en']));

    //                 City::firstOrCreate(
    //                     [
    //                         'city_slug' => $citySlug,
    //                         'governorate_id' => $gov->id
    //                     ],
    //                     [
    //                         'city_name' => $city,
    //                     ]
    //                 );
    //             }
    //         }
    //     };

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Egypt
    //     |--------------------------------------------------------------------------
    //     */
    //     $createData('egypt', ['en' => 'Egypt', 'ar' => 'مصر'], [

    //         'cairo' => [
    //             'name' => ['en' => 'Cairo', 'ar' => 'القاهرة'],
    //             'cities' => [
    //                 ['en' => 'Nasr City', 'ar' => 'مدينة نصر'],
    //                 ['en' => 'Heliopolis', 'ar' => 'مصر الجديدة'],
    //                 ['en' => 'Maadi', 'ar' => 'المعادي'],
    //                 ['en' => 'Shubra', 'ar' => 'شبرا'],
    //             ]
    //         ],

    //         'giza' => [
    //             'name' => ['en' => 'Giza', 'ar' => 'الجيزة'],
    //             'cities' => [
    //                 ['en' => 'Giza', 'ar' => 'الجيزة'],
    //                 ['en' => '6th of October', 'ar' => '6 أكتوبر'],
    //                 ['en' => 'Sheikh Zayed', 'ar' => 'الشيخ زايد'],
    //                 ['en' => 'Haram', 'ar' => 'الهرم'],
    //             ]
    //         ],

    //         'alexandria' => [
    //             'name' => ['en' => 'Alexandria', 'ar' => 'الإسكندرية'],
    //             'cities' => [
    //                 ['en' => 'Alexandria', 'ar' => 'الإسكندرية'],
    //                 ['en' => 'Borg El Arab', 'ar' => 'برج العرب'],
    //             ]
    //         ],

    //         'dakahlia' => [
    //             'name' => ['en' => 'Dakahlia', 'ar' => 'الدقهلية'],
    //             'cities' => [
    //                 ['en' => 'Mansoura', 'ar' => 'المنصورة'],
    //                 ['en' => 'Talkha', 'ar' => 'طلخا'],
    //             ]
    //         ],

    //         'red-sea' => [
    //             'name' => ['en' => 'Red Sea', 'ar' => 'البحر الأحمر'],
    //             'cities' => [
    //                 ['en' => 'Hurghada', 'ar' => 'الغردقة'],
    //                 ['en' => 'Safaga', 'ar' => 'سفاجا'],
    //                 ['en' => 'Marsa Alam', 'ar' => 'مرسى علم'],
    //             ]
    //         ],

    //         'beheira' => [
    //             'name' => ['en' => 'Beheira', 'ar' => 'البحيرة'],
    //             'cities' => [
    //                 ['en' => 'Damanhur', 'ar' => 'دمنهور'],
    //                 ['en' => 'Kafr El Dawwar', 'ar' => 'كفر الدوار'],
    //             ]
    //         ],

    //         'fayoum' => [
    //             'name' => ['en' => 'Fayoum', 'ar' => 'الفيوم'],
    //             'cities' => [
    //                 ['en' => 'Fayoum', 'ar' => 'الفيوم'],
    //                 ['en' => 'Tamiya', 'ar' => 'طامية'],
    //             ]
    //         ],

    //         'gharbia' => [
    //             'name' => ['en' => 'Gharbia', 'ar' => 'الغربية'],
    //             'cities' => [
    //                 ['en' => 'Tanta', 'ar' => 'طنطا'],
    //                 ['en' => 'El Mahalla', 'ar' => 'المحلة الكبرى'],
    //             ]
    //         ],

    //         'ismailia' => [
    //             'name' => ['en' => 'Ismailia', 'ar' => 'الإسماعيلية'],
    //             'cities' => [
    //                 ['en' => 'Ismailia', 'ar' => 'الإسماعيلية'],
    //                 ['en' => 'Fayed', 'ar' => 'فايد'],
    //             ]
    //         ],

    //         'menofia' => [
    //             'name' => ['en' => 'Menofia', 'ar' => 'المنوفية'],
    //             'cities' => [
    //                 ['en' => 'Shebin El Kom', 'ar' => 'شبين الكوم'],
    //                 ['en' => 'Sadat City', 'ar' => 'مدينة السادات'],
    //             ]
    //         ],

    //         'minya' => [
    //             'name' => ['en' => 'Minya', 'ar' => 'المنيا'],
    //             'cities' => [
    //                 ['en' => 'Minya', 'ar' => 'المنيا'],
    //                 ['en' => 'Mallawi', 'ar' => 'ملوي'],
    //             ]
    //         ],

    //         'qalyubia' => [
    //             'name' => ['en' => 'Qalyubia', 'ar' => 'القليوبية'],
    //             'cities' => [
    //                 ['en' => 'Banha', 'ar' => 'بنها'],
    //                 ['en' => 'Shubra El Kheima', 'ar' => 'شبرا الخيمة'],
    //             ]
    //         ],

    //         'new-valley' => [
    //             'name' => ['en' => 'New Valley', 'ar' => 'الوادي الجديد'],
    //             'cities' => [
    //                 ['en' => 'Kharga', 'ar' => 'الخارجة'],
    //                 ['en' => 'Dakhla', 'ar' => 'الداخلة'],
    //             ]
    //         ],

    //         'suez' => [
    //             'name' => ['en' => 'Suez', 'ar' => 'السويس'],
    //             'cities' => [
    //                 ['en' => 'Suez', 'ar' => 'السويس'],
    //             ]
    //         ],

    //         'aswan' => [
    //             'name' => ['en' => 'Aswan', 'ar' => 'أسوان'],
    //             'cities' => [
    //                 ['en' => 'Aswan', 'ar' => 'أسوان'],
    //                 ['en' => 'Kom Ombo', 'ar' => 'كوم أمبو'],
    //             ]
    //         ],

    //         'assiut' => [
    //             'name' => ['en' => 'Assiut', 'ar' => 'أسيوط'],
    //             'cities' => [
    //                 ['en' => 'Assiut', 'ar' => 'أسيوط'],
    //             ]
    //         ],

    //         'beni-suef' => [
    //             'name' => ['en' => 'Beni Suef', 'ar' => 'بني سويف'],
    //             'cities' => [
    //                 ['en' => 'Beni Suef', 'ar' => 'بني سويف'],
    //             ]
    //         ],

    //         'port-said' => [
    //             'name' => ['en' => 'Port Said', 'ar' => 'بورسعيد'],
    //             'cities' => [
    //                 ['en' => 'Port Said', 'ar' => 'بورسعيد'],
    //             ]
    //         ],

    //         'damietta' => [
    //             'name' => ['en' => 'Damietta', 'ar' => 'دمياط'],
    //             'cities' => [
    //                 ['en' => 'Damietta', 'ar' => 'دمياط'],
    //                 ['en' => 'New Damietta', 'ar' => 'دمياط الجديدة'],
    //             ]
    //         ],

    //         'sharqia' => [
    //             'name' => ['en' => 'Sharqia', 'ar' => 'الشرقية'],
    //             'cities' => [
    //                 ['en' => 'Zagazig', 'ar' => 'الزقازيق'],
    //                 ['en' => '10th of Ramadan', 'ar' => 'العاشر من رمضان'],
    //             ]
    //         ],

    //         'south-sinai' => [
    //             'name' => ['en' => 'South Sinai', 'ar' => 'جنوب سيناء'],
    //             'cities' => [
    //                 ['en' => 'Sharm El Sheikh', 'ar' => 'شرم الشيخ'],
    //                 ['en' => 'Dahab', 'ar' => 'دهب'],
    //             ]
    //         ],

    //         'kafr-el-sheikh' => [
    //             'name' => ['en' => 'Kafr El Sheikh', 'ar' => 'كفر الشيخ'],
    //             'cities' => [
    //                 ['en' => 'Kafr El Sheikh', 'ar' => 'كفر الشيخ'],
    //             ]
    //         ],

    //         'matrouh' => [
    //             'name' => ['en' => 'Matrouh', 'ar' => 'مطروح'],
    //             'cities' => [
    //                 ['en' => 'Marsa Matrouh', 'ar' => 'مرسى مطروح'],
    //             ]
    //         ],

    //         'luxor' => [
    //             'name' => ['en' => 'Luxor', 'ar' => 'الأقصر'],
    //             'cities' => [
    //                 ['en' => 'Luxor', 'ar' => 'الأقصر'],
    //             ]
    //         ],

    //         'qena' => [
    //             'name' => ['en' => 'Qena', 'ar' => 'قنا'],
    //             'cities' => [
    //                 ['en' => 'Qena', 'ar' => 'قنا'],
    //             ]
    //         ],

    //         'north-sinai' => [
    //             'name' => ['en' => 'North Sinai', 'ar' => 'شمال سيناء'],
    //             'cities' => [
    //                 ['en' => 'Arish', 'ar' => 'العريش'],
    //             ]
    //         ],

    //         'sohag' => [
    //             'name' => ['en' => 'Sohag', 'ar' => 'سوهاج'],
    //             'cities' => [
    //                 ['en' => 'Sohag', 'ar' => 'سوهاج'],
    //             ]
    //         ],
    //     ]);

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Saudi Arabia
    //     |--------------------------------------------------------------------------
    //     */
    //     $createData('saudi-arabia', ['en' => 'Saudi Arabia', 'ar' => 'السعودية'], [

    //         'riyadh' => [
    //             'name' => ['en' => 'Riyadh Region', 'ar' => 'منطقة الرياض'],
    //             'cities' => [
    //                 ['en' => 'Riyadh', 'ar' => 'الرياض'],
    //                 ['en' => 'Al Kharj', 'ar' => 'الخرج'],
    //             ]
    //         ],

    //         'makkah' => [
    //             'name' => ['en' => 'Makkah Region', 'ar' => 'منطقة مكة'],
    //             'cities' => [
    //                 ['en' => 'Mecca', 'ar' => 'مكة'],
    //                 ['en' => 'Jeddah', 'ar' => 'جدة'],
    //                 ['en' => 'Taif', 'ar' => 'الطائف'],
    //             ]
    //         ],

    //         'eastern' => [
    //             'name' => ['en' => 'Eastern Province', 'ar' => 'المنطقة الشرقية'],
    //             'cities' => [
    //                 ['en' => 'Dammam', 'ar' => 'الدمام'],
    //                 ['en' => 'Khobar', 'ar' => 'الخبر'],
    //             ]
    //         ],
    //     ]);

    //     /*
    //     |--------------------------------------------------------------------------
    //     | UAE
    //     |--------------------------------------------------------------------------
    //     */
    //     $createData('uae', ['en' => 'UAE', 'ar' => 'الإمارات'], [

    //         'dubai' => [
    //             'name' => ['en' => 'Dubai', 'ar' => 'دبي'],
    //             'cities' => [
    //                 ['en' => 'Dubai', 'ar' => 'دبي'],
    //             ]
    //         ],

    //         'abu-dhabi' => [
    //             'name' => ['en' => 'Abu Dhabi', 'ar' => 'أبوظبي'],
    //             'cities' => [
    //                 ['en' => 'Abu Dhabi', 'ar' => 'أبوظبي'],
    //                 ['en' => 'Al Ain', 'ar' => 'العين'],
    //             ]
    //         ],
    //     ]);

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Kuwait
    //     |--------------------------------------------------------------------------
    //     */
    //     $createData('kuwait', ['en' => 'Kuwait', 'ar' => 'الكويت'], [

    //         'capital' => [
    //             'name' => ['en' => 'Capital', 'ar' => 'العاصمة'],
    //             'cities' => [
    //                 ['en' => 'Kuwait City', 'ar' => 'مدينة الكويت'],
    //             ]
    //         ],

    //         'hawalli' => [
    //             'name' => ['en' => 'Hawalli', 'ar' => 'حولي'],
    //             'cities' => [
    //                 ['en' => 'Hawalli', 'ar' => 'حولي'],
    //                 ['en' => 'Salmiya', 'ar' => 'السالمية'],
    //             ]
    //         ],
    //     ]);

    //     /*
    //     |--------------------------------------------------------------------------
    //     | Qatar
    //     |--------------------------------------------------------------------------
    //     */
    //     $createData('qatar', ['en' => 'Qatar', 'ar' => 'قطر'], [

    //         'doha' => [
    //             'name' => ['en' => 'Doha', 'ar' => 'الدوحة'],
    //             'cities' => [
    //                 ['en' => 'Doha', 'ar' => 'الدوحة'],
    //             ]
    //         ],

    //         'al-rayyan' => [
    //             'name' => ['en' => 'Al Rayyan', 'ar' => 'الريان'],
    //             'cities' => [
    //                 ['en' => 'Al Rayyan', 'ar' => 'الريان'],
    //             ]
    //         ],
    //     ]);
    // }
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Helper Function
        |--------------------------------------------------------------------------
        */
        $createData = function ($countrySlug, $countryName, $data) {

            // إنشاء الدولة أو جلبها إذا كانت موجودة
            $country = Country::firstOrCreate(
                ['country_slug' => $countrySlug],
                ['country_name' => $countryName]
            );

            foreach ($data as $slug => $govData) {

                // إنشاء المحافظة/المنطقة
                $gov = Governorate::firstOrCreate(
                    [
                        'governorate_slug' => $slug,
                        'country_id' => $country->id
                    ],
                    [
                        'governorate_name' => $govData['name'],
                    ]
                );

                foreach ($govData['cities'] as $city) {

                    // إنشاء الـ Slug الآمن للمدينة باستخدام Laravel Str
                    $citySlug = Str::slug($city['en']);

                    // إنشاء المدينة
                    City::firstOrCreate(
                        [
                            'city_slug' => $citySlug,
                            'governorate_id' => $gov->id
                        ],
                        [
                            'city_name' => $city,
                        ]
                    );
                }
            }
        };

        /*
        |--------------------------------------------------------------------------
        | Egypt (مصر)
        |--------------------------------------------------------------------------
        */
        $createData('egypt', ['en' => 'Egypt', 'ar' => 'مصر'], [

            'cairo' => [
                'name' => ['en' => 'Cairo', 'ar' => 'القاهرة'],
                'cities' => [
                    ['en' => 'Nasr City', 'ar' => 'مدينة نصر'],
                    ['en' => 'Heliopolis', 'ar' => 'مصر الجديدة'],
                    ['en' => 'Maadi', 'ar' => 'المعادي'],
                    ['en' => 'Shubra', 'ar' => 'شبرا'],
                ]
            ],

            'giza' => [
                'name' => ['en' => 'Giza', 'ar' => 'الجيزة'],
                'cities' => [
                    ['en' => 'Giza', 'ar' => 'الجيزة'],
                    ['en' => '6th of October', 'ar' => '6 أكتوبر'],
                    ['en' => 'Sheikh Zayed', 'ar' => 'الشيخ زايد'],
                    ['en' => 'Haram', 'ar' => 'الهرم'],
                ]
            ],

            'alexandria' => [
                'name' => ['en' => 'Alexandria', 'ar' => 'الإسكندرية'],
                'cities' => [
                    ['en' => 'Alexandria', 'ar' => 'الإسكندرية'],
                    ['en' => 'Borg El Arab', 'ar' => 'برج العرب'],
                ]
            ],

            'dakahlia' => [
                'name' => ['en' => 'Dakahlia', 'ar' => 'الدقهلية'],
                'cities' => [
                    ['en' => 'Mansoura', 'ar' => 'المنصورة'],
                    ['en' => 'Talkha', 'ar' => 'طلخا'],
                ]
            ],

            'red-sea' => [
                'name' => ['en' => 'Red Sea', 'ar' => 'البحر الأحمر'],
                'cities' => [
                    ['en' => 'Hurghada', 'ar' => 'الغردقة'],
                    ['en' => 'Safaga', 'ar' => 'سفاجا'],
                    ['en' => 'Marsa Alam', 'ar' => 'مرسى علم'],
                ]
            ],

            'beheira' => [
                'name' => ['en' => 'Beheira', 'ar' => 'البحيرة'],
                'cities' => [
                    ['en' => 'Damanhur', 'ar' => 'دمنهور'],
                    ['en' => 'Kafr El Dawwar', 'ar' => 'كفر الدوار'],
                ]
            ],

            'fayoum' => [
                'name' => ['en' => 'Fayoum', 'ar' => 'الفيوم'],
                'cities' => [
                    ['en' => 'Fayoum', 'ar' => 'الفيوم'],
                    ['en' => 'Tamiya', 'ar' => 'طامية'],
                ]
            ],

            'gharbia' => [
                'name' => ['en' => 'Gharbia', 'ar' => 'الغربية'],
                'cities' => [
                    ['en' => 'Tanta', 'ar' => 'طنطا'],
                    ['en' => 'El Mahalla', 'ar' => 'المحلة الكبرى'],
                ]
            ],

            'ismailia' => [
                'name' => ['en' => 'Ismailia', 'ar' => 'الإسماعيلية'],
                'cities' => [
                    ['en' => 'Ismailia', 'ar' => 'الإسماعيلية'],
                    ['en' => 'Fayed', 'ar' => 'فايد'],
                ]
            ],

            'menofia' => [
                'name' => ['en' => 'Menofia', 'ar' => 'المنوفية'],
                'cities' => [
                    ['en' => 'Shebin El Kom', 'ar' => 'شبين الكوم'],
                    ['en' => 'Sadat City', 'ar' => 'مدينة السادات'],
                ]
            ],

            'minya' => [
                'name' => ['en' => 'Minya', 'ar' => 'المنيا'],
                'cities' => [
                    ['en' => 'Minya', 'ar' => 'المنيا'],
                    ['en' => 'Mallawi', 'ar' => 'ملوي'],
                ]
            ],

            'qalyubia' => [
                'name' => ['en' => 'Qalyubia', 'ar' => 'القليوبية'],
                'cities' => [
                    ['en' => 'Banha', 'ar' => 'بنها'],
                    ['en' => 'Shubra El Kheima', 'ar' => 'شبرا الخيمة'],
                ]
            ],

            'new-valley' => [
                'name' => ['en' => 'New Valley', 'ar' => 'الوادي الجديد'],
                'cities' => [
                    ['en' => 'Kharga', 'ar' => 'الخارجة'],
                    ['en' => 'Dakhla', 'ar' => 'الداخلة'],
                ]
            ],

            'suez' => [
                'name' => ['en' => 'Suez', 'ar' => 'السويس'],
                'cities' => [
                    ['en' => 'Suez', 'ar' => 'السويس'],
                ]
            ],

            'aswan' => [
                'name' => ['en' => 'Aswan', 'ar' => 'أسوان'],
                'cities' => [
                    ['en' => 'Aswan', 'ar' => 'أسوان'],
                    ['en' => 'Kom Ombo', 'ar' => 'كوم أمبو'],
                ]
            ],

            'assiut' => [
                'name' => ['en' => 'Assiut', 'ar' => 'أسيوط'],
                'cities' => [
                    ['en' => 'Assiut', 'ar' => 'أسيوط'],
                ]
            ],

            'beni-suef' => [
                'name' => ['en' => 'Beni Suef', 'ar' => 'بني سويف'],
                'cities' => [
                    ['en' => 'Beni Suef', 'ar' => 'بني سويف'],
                ]
            ],

            'port-said' => [
                'name' => ['en' => 'Port Said', 'ar' => 'بورسعيد'],
                'cities' => [
                    ['en' => 'Port Said', 'ar' => 'بورسعيد'],
                ]
            ],

            'damietta' => [
                'name' => ['en' => 'Damietta', 'ar' => 'دمياط'],
                'cities' => [
                    ['en' => 'Damietta', 'ar' => 'دمياط'],
                    ['en' => 'New Damietta', 'ar' => 'دمياط الجديدة'],
                ]
            ],

            'sharqia' => [
                'name' => ['en' => 'Sharqia', 'ar' => 'الشرقية'],
                'cities' => [
                    ['en' => 'Zagazig', 'ar' => 'الزقازيق'],
                    ['en' => '10th of Ramadan', 'ar' => 'العاشر من رمضان'],
                ]
            ],

            'south-sinai' => [
                'name' => ['en' => 'South Sinai', 'ar' => 'جنوب سيناء'],
                'cities' => [
                    ['en' => 'Sharm El Sheikh', 'ar' => 'شرم الشيخ'],
                    ['en' => 'Dahab', 'ar' => 'دهب'],
                ]
            ],

            'kafr-el-sheikh' => [
                'name' => ['en' => 'Kafr El Sheikh', 'ar' => 'كفر الشيخ'],
                'cities' => [
                    ['en' => 'Kafr El Sheikh', 'ar' => 'كفر الشيخ'],
                ]
            ],

            'matrouh' => [
                'name' => ['en' => 'Matrouh', 'ar' => 'مطروح'],
                'cities' => [
                    ['en' => 'Marsa Matrouh', 'ar' => 'مرسى مطروح'],
                ]
            ],

            'luxor' => [
                'name' => ['en' => 'Luxor', 'ar' => 'الأقصر'],
                'cities' => [
                    ['en' => 'Luxor', 'ar' => 'الأقصر'],
                ]
            ],

            'qena' => [
                'name' => ['en' => 'Qena', 'ar' => 'قنا'],
                'cities' => [
                    ['en' => 'Qena', 'ar' => 'قنا'],
                ]
            ],

            'north-sinai' => [
                'name' => ['en' => 'North Sinai', 'ar' => 'شمال سيناء'],
                'cities' => [
                    ['en' => 'Arish', 'ar' => 'العريش'],
                ]
            ],

            'sohag' => [
                'name' => ['en' => 'Sohag', 'ar' => 'سوهاج'],
                'cities' => [
                    ['en' => 'Sohag', 'ar' => 'سوهاج'],
                ]
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Saudi Arabia (السعودية)
        |--------------------------------------------------------------------------
        */
        $createData('saudi-arabia', ['en' => 'Saudi Arabia', 'ar' => 'السعودية'], [

            'riyadh' => [
                'name' => ['en' => 'Riyadh Region', 'ar' => 'منطقة الرياض'],
                'cities' => [
                    ['en' => 'Riyadh', 'ar' => 'الرياض'],
                    ['en' => 'Al Kharj', 'ar' => 'الخرج'],
                ]
            ],

            'makkah' => [
                'name' => ['en' => 'Makkah Region', 'ar' => 'منطقة مكة'],
                'cities' => [
                    ['en' => 'Mecca', 'ar' => 'مكة'],
                    ['en' => 'Jeddah', 'ar' => 'جدة'],
                    ['en' => 'Taif', 'ar' => 'الطائف'],
                ]
            ],

            'eastern' => [
                'name' => ['en' => 'Eastern Province', 'ar' => 'المنطقة الشرقية'],
                'cities' => [
                    ['en' => 'Dammam', 'ar' => 'الدمام'],
                    ['en' => 'Khobar', 'ar' => 'الخبر'],
                ]
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | UAE (الإمارات)
        |--------------------------------------------------------------------------
        */
        $createData('uae', ['en' => 'UAE', 'ar' => 'الإمارات'], [

            'dubai' => [
                'name' => ['en' => 'Dubai', 'ar' => 'دبي'],
                'cities' => [
                    ['en' => 'Dubai', 'ar' => 'دبي'],
                ]
            ],

            'abu-dhabi' => [
                'name' => ['en' => 'Abu Dhabi', 'ar' => 'أبوظبي'],
                'cities' => [
                    ['en' => 'Abu Dhabi', 'ar' => 'أبوظبي'],
                    ['en' => 'Al Ain', 'ar' => 'العين'],
                ]
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Kuwait (الكويت)
        |--------------------------------------------------------------------------
        */
        $createData('kuwait', ['en' => 'Kuwait', 'ar' => 'الكويت'], [

            'capital' => [
                'name' => ['en' => 'Capital', 'ar' => 'العاصمة'],
                'cities' => [
                    ['en' => 'Kuwait City', 'ar' => 'مدينة الكويت'],
                ]
            ],

            'hawalli' => [
                'name' => ['en' => 'Hawalli', 'ar' => 'حولي'],
                'cities' => [
                    ['en' => 'Hawalli', 'ar' => 'حولي'],
                    ['en' => 'Salmiya', 'ar' => 'السالمية'],
                ]
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Qatar (قطر)
        |--------------------------------------------------------------------------
        */
        $createData('qatar', ['en' => 'Qatar', 'ar' => 'قطر'], [

            'doha' => [
                'name' => ['en' => 'Doha', 'ar' => 'الدوحة'],
                'cities' => [
                    ['en' => 'Doha', 'ar' => 'الدوحة'],
                ]
            ],

            'al-rayyan' => [
                'name' => ['en' => 'Al Rayyan', 'ar' => 'الريان'],
                'cities' => [
                    ['en' => 'Al Rayyan', 'ar' => 'الريان'],
                ]
            ],
        ]);
    }
}
