<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //     $categories = [
    //         [
    //             'category_name' => [
    //                 'en' => 'Antibiotics',
    //                 'ar' => 'المضادات الحيوية',
    //             ],
    //             'category_description' => [
    //                 'en' => 'Medicines used to treat bacterial infections by killing or inhibiting the growth of bacteria.',
    //                 'ar' => 'أدوية تُستخدم لعلاج الالتهابات البكتيرية عن طريق قتل البكتيريا أو تثبيط نموها.',
    //             ],
    //         ],
    //         [
    //             'category_name' => [
    //                 'en' => 'Analgesics',
    //                 'ar' => 'المسكنات',
    //             ],
    //             'category_description' => [
    //                 'en' => 'Pain-relieving medicines that reduce or eliminate pain without causing loss of consciousness.',
    //                 'ar' => 'أدوية مسكنة للألم تعمل على تخفيف الألم أو إزالته دون التسبب في فقدان الوعي.',
    //             ],
    //         ],
    //         [
    //             'category_name' => [
    //                 'en' => 'Antipyretics',
    //                 'ar' => 'خافضات الحرارة',
    //             ],
    //             'category_description' => [
    //                 'en' => 'Medicines used to reduce fever and lower elevated body temperature.',
    //                 'ar' => 'أدوية تُستخدم لتخفيض الحرارة وخفض درجة حرارة الجسم المرتفعة.',
    //             ],
    //         ],
    //         [
    //             'category_name' => [
    //                 'en' => 'Antifungals',
    //                 'ar' => 'مضادات الفطريات',
    //             ],
    //             'category_description' => [
    //                 'en' => 'Medicines used to treat fungal infections affecting the skin, nails, or internal organs.',
    //                 'ar' => 'أدوية تُستخدم لعلاج الالتهابات الفطرية التي تصيب الجلد والأظافر أو الأعضاء الداخلية.',
    //             ],
    //         ],
    //         [
    //             'category_name' => [
    //                 'en' => 'Antivirals',
    //                 'ar' => 'مضادات الفيروسات',
    //             ],
    //             'category_description' => [
    //                 'en' => 'Medicines that inhibit the development of viral infections in the body.',
    //                 'ar' => 'أدوية تعمل على تثبيط تطور الالتهابات الفيروسية داخل الجسم.',
    //             ],
    //         ],
    //         [
    //             'category_name' => [
    //                 'en' => 'Antihypertensives',
    //                 'ar' => 'خافضات ضغط الدم',
    //             ],
    //             'category_description' => [
    //                 'en' => 'Medicines used to treat high blood pressure and reduce the risk of cardiovascular complications.',
    //                 'ar' => 'أدوية تُستخدم لعلاج ارتفاع ضغط الدم والحد من مخاطر المضاعفات القلبية الوعائية.',
    //             ],
    //         ],
    //         [
    //             'category_name' => [
    //                 'en' => 'Antidiabetics',
    //                 'ar' => 'أدوية السكري',
    //             ],
    //             'category_description' => [
    //                 'en' => 'Medicines used to manage blood sugar levels in patients with diabetes mellitus.',
    //                 'ar' => 'أدوية تُستخدم للتحكم في مستويات السكر في الدم لدى مرضى داء السكري.',
    //             ],
    //         ],
    //         [
    //             'category_name' => [
    //                 'en' => 'Antihistamines',
    //                 'ar' => 'مضادات الهيستامين',
    //             ],
    //             'category_description' => [
    //                 'en' => 'Medicines that block histamine receptors to relieve allergy symptoms such as sneezing and itching.',
    //                 'ar' => 'أدوية تعمل على حجب مستقبلات الهيستامين لتخفيف أعراض الحساسية كالعطس والحكة.',
    //             ],
    //         ],
    //         [
    //             'category_name' => [
    //                 'en' => 'Antacids',
    //                 'ar' => 'مضادات الحموضة',
    //             ],
    //             'category_description' => [
    //                 'en' => 'Medicines that neutralize stomach acid to relieve heartburn, indigestion, and acid reflux.',
    //                 'ar' => 'أدوية تعمل على تعادل حمض المعدة لتخفيف حرقة المعدة وعسر الهضم وارتجاع الحمض.',
    //             ],
    //         ],
    //         [
    //             'category_name' => [
    //                 'en' => 'Vitamins & Supplements',
    //                 'ar' => 'الفيتامينات والمكملات الغذائية',
    //             ],
    //             'category_description' => [
    //                 'en' => 'Nutritional supplements and vitamins used to support overall health and compensate for dietary deficiencies.',
    //                 'ar' => 'مكملات غذائية وفيتامينات تُستخدم لدعم الصحة العامة وتعويض النقص الغذائي.',
    //             ],
    //         ],
    //     ];

    //     foreach ($categories as $data) {
    //         $data['category_slug'] = Str::slug($data['category_name']['en']);
    //         Category::create($data);
    //     }
    // }
    public function run(): void
    {
        $categories = [
            // 1 - Analgesics & Antipyretics (المسكنات وخافضات الحرارة)
            [
                'category_name' => [
                    'en' => 'Analgesics & Antipyretics',
                    'ar' => 'المسكنات وخافضات الحرارة',
                ],
                'category_description' => [
                    'en' => 'Pain-relieving and fever-reducing medicines that eliminate pain without causing loss of consciousness.',
                    'ar' => 'أدوية مسكنة للألم وخافضة للحرارة تعمل على تخفيف الألم دون التسبب في فقدان الوعي.',
                ],
            ],
            // 2 - Antibiotics (المضادات الحيوية)
            [
                'category_name' => [
                    'en' => 'Antibiotics',
                    'ar' => 'المضادات الحيوية',
                ],
                'category_description' => [
                    'en' => 'Medicines used to treat bacterial infections by killing or inhibiting the growth of bacteria.',
                    'ar' => 'أدوية تُستخدم لعلاج الالتهابات البكتيرية عن طريق قتل البكتيريا أو تثبيط نموها.',
                ],
            ],
            // 3 - Cardiovascular (أدوية القلب والأوعية الدموية)
            [
                'category_name' => [
                    'en' => 'Cardiovascular',
                    'ar' => 'أدوية القلب والأوعية الدموية',
                ],
                'category_description' => [
                    'en' => 'Medicines used to treat high blood pressure, cholesterol, and reduce the risk of heart complications.',
                    'ar' => 'أدوية تُستخدم لعلاج ضغط الدم والكوليسترول والحد من مخاطر المضاعفات القلبية.',
                ],
            ],
            // 4 - Antidiabetics (أدوية السكري)
            [
                'category_name' => [
                    'en' => 'Antidiabetics',
                    'ar' => 'أدوية السكري',
                ],
                'category_description' => [
                    'en' => 'Medicines used to manage blood sugar levels in patients with diabetes mellitus.',
                    'ar' => 'أدوية تُستخدم للتحكم في مستويات السكر في الدم لدى مرضى داء السكري.',
                ],
            ],
            // 5 - Respiratory (أدوية الجهاز التنفسي)
            [
                'category_name' => [
                    'en' => 'Respiratory',
                    'ar' => 'أدوية الجهاز التنفسي',
                ],
                'category_description' => [
                    'en' => 'Medicines used to treat asthma, allergies, and chronic obstructive pulmonary disease (COPD).',
                    'ar' => 'أدوية تستخدم لعلاج الربو، الحساسية، وأمراض الانسداد الرئوي المزمن.',
                ],
            ],
            // 6 - Gastrointestinal (أدوية الجهاز الهضمي)
            [
                'category_name' => [
                    'en' => 'Gastrointestinal',
                    'ar' => 'أدوية الجهاز الهضمي',
                ],
                'category_description' => [
                    'en' => 'Medicines that neutralize stomach acid, relieve indigestion, acid reflux, nausea, and vomiting.',
                    'ar' => 'أدوية تُستخدم لعلاج عسر الهضم، ارتجاع المريء، الغثيان، والقيء.',
                ],
            ],
            // 7 - Neurological / CNS (أدوية الجهاز العصبي)
            [
                'category_name' => [
                    'en' => 'Neurological / CNS',
                    'ar' => 'أدوية الجهاز العصبي',
                ],
                'category_description' => [
                    'en' => 'Medications for conditions affecting the brain and nervous system, including anxiety and depression.',
                    'ar' => 'أدوية مخصصة للحالات التي تؤثر على الدماغ والجهاز العصبي مثل القلق والاكتئاب.',
                ],
            ],
            // 8 - Dermatology (الأدوية الجلدية)
            [
                'category_name' => [
                    'en' => 'Dermatology',
                    'ar' => 'الأدوية الجلدية',
                ],
                'category_description' => [
                    'en' => 'Creams, ointments, and medications used to treat skin conditions and infections.',
                    'ar' => 'كريمات ومراهم وأدوية تستخدم لعلاج الأمراض والالتهابات الجلدية.',
                ],
            ],
            // 9 - Vitamins & Supplements (الفيتامينات والمكملات الغذائية)
            [
                'category_name' => [
                    'en' => 'Vitamins & Supplements',
                    'ar' => 'الفيتامينات والمكملات الغذائية',
                ],
                'category_description' => [
                    'en' => 'Nutritional supplements and vitamins used to support overall health and compensate for dietary deficiencies.',
                    'ar' => 'مكملات غذائية وفيتامينات تُستخدم لدعم الصحة العامة وتعويض النقص الغذائي.',
                ],
            ],
            // 10 - Hormonal / Endocrine (الأدوية الهرمونية)
            [
                'category_name' => [
                    'en' => 'Hormonal / Endocrine',
                    'ar' => 'الأدوية الهرمونية',
                ],
                'category_description' => [
                    'en' => 'Medications that regulate hormones such as thyroid treatments and corticosteroids.',
                    'ar' => 'أدوية تعمل على تنظيم الهرمونات مثل علاجات الغدة الدرقية والكورتيزون.',
                ],
            ],
        ];

        foreach ($categories as $data) {
            // إذا كنت تستخدم حزمة مثل Spatie Translatable، فهذا السطر صحيح.
            // وإذا كانت القاعدة لا تدعم مصفوفات مباشرة، قد تحتاج لاستخدام json_encode كما فعلت في الأدوية.
            $data['category_slug'] = Str::slug($data['category_name']['en']);
            Category::create($data);
        }
    }
}
