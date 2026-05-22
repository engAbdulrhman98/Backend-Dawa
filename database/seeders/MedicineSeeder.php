<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //     $medicines = [
    //         // Category 1 - Pain Relievers / Analgesics
    //         [
    //             'category_id' => 1,
    //             'medicine_name' => json_encode(['en' => 'Paracetamol', 'ar' => 'باراسيتامول']),
    //             'medicine_description' => json_encode(['en' => 'Used to relieve pain and reduce fever.', 'ar' => 'يستخدم لتخفيف الألم وخفض الحرارة.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 1-2 tablets every 4-6 hours.', 'ar' => 'يؤخذ 1-2 قرص كل 4-6 ساعات.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Rare, may include nausea or rash.', 'ar' => 'نادرة، قد تشمل الغثيان أو الطفح الجلدي.']),
    //             'medicine_slug' => Str::slug('Paracetamol'),
    //             'medicine_price' => 55.50,
    //         ],
    //         [
    //             'category_id' => 1,
    //             'medicine_name' => json_encode(['en' => 'Ibuprofen', 'ar' => 'إيبوبروفين']),
    //             'medicine_description' => json_encode(['en' => 'Anti-inflammatory drug used for pain relief.', 'ar' => 'دواء مضاد للالتهابات يستخدم لتخفيف الألم.']),
    //             'medicine_usage' => json_encode(['en' => 'Take after food every 6-8 hours.', 'ar' => 'يؤخذ بعد الطعام كل 6-8 ساعات.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Stomach pain, heartburn.', 'ar' => 'ألم المعدة، حرقة.']),
    //             'medicine_slug' => Str::slug('Ibuprofen'),
    //             'medicine_price' => 25.50,
    //         ],
    //         [
    //             'category_id' => 1,
    //             'medicine_name' => json_encode(['en' => 'Aspirin', 'ar' => 'أسبرين']),
    //             'medicine_description' => json_encode(['en' => 'Used for pain relief, fever reduction, and anti-inflammation.', 'ar' => 'يستخدم لتخفيف الألم وخفض الحرارة ومكافحة الالتهاب.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 1 tablet every 4-6 hours with food.', 'ar' => 'يؤخذ قرص واحد كل 4-6 ساعات مع الطعام.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Stomach irritation, bleeding risk.', 'ar' => 'تهيج المعدة، خطر النزيف.']),
    //             'medicine_slug' => Str::slug('Aspirin'),
    //             'medicine_price' => 18.00,
    //         ],
    //         [
    //             'category_id' => 1,
    //             'medicine_name' => json_encode(['en' => 'Diclofenac', 'ar' => 'ديكلوفيناك']),
    //             'medicine_description' => json_encode(['en' => 'NSAID used to relieve pain, swelling, and joint stiffness.', 'ar' => 'مضاد للالتهابات يستخدم لتخفيف الألم والتورم وتصلب المفاصل.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 50mg tablet 2-3 times daily with food.', 'ar' => 'يؤخذ قرص 50 مجم 2-3 مرات يومياً مع الطعام.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Nausea, indigestion, stomach pain.', 'ar' => 'غثيان، عسر هضم، ألم المعدة.']),
    //             'medicine_slug' => Str::slug('Diclofenac'),
    //             'medicine_price' => 30.00,
    //         ],
    //         [
    //             'category_id' => 1,
    //             'medicine_name' => json_encode(['en' => 'Tramadol', 'ar' => 'ترامادول']),
    //             'medicine_description' => json_encode(['en' => 'Opioid pain medication used to treat moderate to severe pain.', 'ar' => 'مسكن أوبيويدي يستخدم لعلاج الألم المتوسط إلى الشديد.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 50-100mg every 4-6 hours as needed.', 'ar' => 'يؤخذ 50-100 مجم كل 4-6 ساعات حسب الحاجة.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Dizziness, nausea, constipation, drowsiness.', 'ar' => 'دوخة، غثيان، إمساك، نعاس.']),
    //             'medicine_slug' => Str::slug('Tramadol'),
    //             'medicine_price' => 75.00,
    //         ],

    //         // Category 2 - Antibiotics
    //         [
    //             'category_id' => 2,
    //             'medicine_name' => json_encode(['en' => 'Amoxicillin', 'ar' => 'أموكسيسيلين']),
    //             'medicine_description' => json_encode(['en' => 'Penicillin antibiotic used to treat bacterial infections.', 'ar' => 'مضاد حيوي من البنسلين يستخدم لعلاج الالتهابات البكتيرية.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 500mg every 8 hours for 7-10 days.', 'ar' => 'يؤخذ 500 مجم كل 8 ساعات لمدة 7-10 أيام.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Diarrhea, rash, nausea.', 'ar' => 'إسهال، طفح جلدي، غثيان.']),
    //             'medicine_slug' => Str::slug('Amoxicillin'),
    //             'medicine_price' => 45.00,
    //         ],
    //         [
    //             'category_id' => 2,
    //             'medicine_name' => json_encode(['en' => 'Azithromycin', 'ar' => 'أزيثرومايسين']),
    //             'medicine_description' => json_encode(['en' => 'Macrolide antibiotic for respiratory and skin infections.', 'ar' => 'مضاد حيوي ماكروليدي لعلاج التهابات الجهاز التنفسي والجلد.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 500mg once daily for 3 days.', 'ar' => 'يؤخذ 500 مجم مرة واحدة يومياً لمدة 3 أيام.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Nausea, abdominal pain, diarrhea.', 'ar' => 'غثيان، ألم في البطن، إسهال.']),
    //             'medicine_slug' => Str::slug('Azithromycin'),
    //             'medicine_price' => 65.00,
    //         ],
    //         [
    //             'category_id' => 2,
    //             'medicine_name' => json_encode(['en' => 'Ciprofloxacin', 'ar' => 'سيبروفلوكساسين']),
    //             'medicine_description' => json_encode(['en' => 'Fluoroquinolone antibiotic for urinary and respiratory infections.', 'ar' => 'مضاد حيوي فلوروكينولوني لالتهابات المسالك البولية والجهاز التنفسي.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 500mg twice daily for 7 days.', 'ar' => 'يؤخذ 500 مجم مرتين يومياً لمدة 7 أيام.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Nausea, diarrhea, dizziness.', 'ar' => 'غثيان، إسهال، دوخة.']),
    //             'medicine_slug' => Str::slug('Ciprofloxacin'),
    //             'medicine_price' => 55.00,
    //         ],
    //         [
    //             'category_id' => 2,
    //             'medicine_name' => json_encode(['en' => 'Doxycycline', 'ar' => 'دوكسيسيكلين']),
    //             'medicine_description' => json_encode(['en' => 'Tetracycline antibiotic for infections and acne.', 'ar' => 'مضاد حيوي تتراسيكلين لعلاج الالتهابات وحب الشباب.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 100mg once or twice daily with water.', 'ar' => 'يؤخذ 100 مجم مرة أو مرتين يومياً مع الماء.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Photosensitivity, nausea, esophageal irritation.', 'ar' => 'حساسية للضوء، غثيان، تهيج المريء.']),
    //             'medicine_slug' => Str::slug('Doxycycline'),
    //             'medicine_price' => 40.00,
    //         ],
    //         [
    //             'category_id' => 2,
    //             'medicine_name' => json_encode(['en' => 'Metronidazole', 'ar' => 'ميترونيدازول']),
    //             'medicine_description' => json_encode(['en' => 'Antibiotic for bacterial and parasitic infections.', 'ar' => 'مضاد حيوي للالتهابات البكتيرية والطفيلية.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 400-500mg three times daily for 5-7 days.', 'ar' => 'يؤخذ 400-500 مجم ثلاث مرات يومياً لمدة 5-7 أيام.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Metallic taste, nausea, headache.', 'ar' => 'طعم معدني، غثيان، صداع.']),
    //             'medicine_slug' => Str::slug('Metronidazole'),
    //             'medicine_price' => 20.00,
    //         ],

    //         // Category 3 - Cardiovascular
    //         [
    //             'category_id' => 3,
    //             'medicine_name' => json_encode(['en' => 'Atorvastatin', 'ar' => 'أتورفاستاتين']),
    //             'medicine_description' => json_encode(['en' => 'Statin used to lower cholesterol and prevent heart disease.', 'ar' => 'ستاتين يستخدم لخفض الكوليسترول والوقاية من أمراض القلب.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 10-80mg once daily at any time.', 'ar' => 'يؤخذ 10-80 مجم مرة واحدة يومياً في أي وقت.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Muscle pain, liver enzyme elevation.', 'ar' => 'ألم العضلات، ارتفاع إنزيمات الكبد.']),
    //             'medicine_slug' => Str::slug('Atorvastatin'),
    //             'medicine_price' => 90.00,
    //         ],
    //         [
    //             'category_id' => 3,
    //             'medicine_name' => json_encode(['en' => 'Amlodipine', 'ar' => 'أملوديبين']),
    //             'medicine_description' => json_encode(['en' => 'Calcium channel blocker for hypertension and angina.', 'ar' => 'حاصر قنوات الكالسيوم لعلاج ارتفاع ضغط الدم والذبحة الصدرية.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 5-10mg once daily.', 'ar' => 'يؤخذ 5-10 مجم مرة واحدة يومياً.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Swelling in ankles, flushing, palpitations.', 'ar' => 'تورم الكاحلين، احمرار، خفقان.']),
    //             'medicine_slug' => Str::slug('Amlodipine'),
    //             'medicine_price' => 60.00,
    //         ],
    //         [
    //             'category_id' => 3,
    //             'medicine_name' => json_encode(['en' => 'Metoprolol', 'ar' => 'ميتوبرولول']),
    //             'medicine_description' => json_encode(['en' => 'Beta-blocker for high blood pressure and heart failure.', 'ar' => 'حاصر بيتا لعلاج ارتفاع ضغط الدم وفشل القلب.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 25-100mg once or twice daily.', 'ar' => 'يؤخذ 25-100 مجم مرة أو مرتين يومياً.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Fatigue, dizziness, cold extremities.', 'ar' => 'تعب، دوخة، برودة الأطراف.']),
    //             'medicine_slug' => Str::slug('Metoprolol'),
    //             'medicine_price' => 50.00,
    //         ],
    //         [
    //             'category_id' => 3,
    //             'medicine_name' => json_encode(['en' => 'Lisinopril', 'ar' => 'ليسينوبريل']),
    //             'medicine_description' => json_encode(['en' => 'ACE inhibitor for hypertension and heart failure.', 'ar' => 'مثبط ACE لعلاج ارتفاع ضغط الدم وفشل القلب.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 10-40mg once daily.', 'ar' => 'يؤخذ 10-40 مجم مرة واحدة يومياً.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Dry cough, dizziness, high potassium.', 'ar' => 'سعال جاف، دوخة، ارتفاع البوتاسيوم.']),
    //             'medicine_slug' => Str::slug('Lisinopril'),
    //             'medicine_price' => 35.00,
    //         ],
    //         [
    //             'category_id' => 3,
    //             'medicine_name' => json_encode(['en' => 'Warfarin', 'ar' => 'وارفارين']),
    //             'medicine_description' => json_encode(['en' => 'Anticoagulant to prevent blood clots and stroke.', 'ar' => 'مضاد تخثر للوقاية من جلطات الدم والسكتة الدماغية.']),
    //             'medicine_usage' => json_encode(['en' => 'Dose adjusted per INR; typically 2-10mg daily.', 'ar' => 'الجرعة تُعدَّل وفق INR؛ عادةً 2-10 مجم يومياً.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Bleeding, bruising, rare skin necrosis.', 'ar' => 'نزيف، كدمات، نخر الجلد نادراً.']),
    //             'medicine_slug' => Str::slug('Warfarin'),
    //             'medicine_price' => 22.00,
    //         ],

    //         // Category 4 - Diabetes
    //         [
    //             'category_id' => 4,
    //             'medicine_name' => json_encode(['en' => 'Metformin', 'ar' => 'ميتفورمين']),
    //             'medicine_description' => json_encode(['en' => 'First-line medication for type 2 diabetes.', 'ar' => 'الدواء الأول لعلاج السكري من النوع الثاني.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 500-1000mg twice daily with meals.', 'ar' => 'يؤخذ 500-1000 مجم مرتين يومياً مع الوجبات.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Nausea, diarrhea, stomach upset.', 'ar' => 'غثيان، إسهال، اضطراب في المعدة.']),
    //             'medicine_slug' => Str::slug('Metformin'),
    //             'medicine_price' => 28.00,
    //         ],
    //         [
    //             'category_id' => 4,
    //             'medicine_name' => json_encode(['en' => 'Glibenclamide', 'ar' => 'غليبنكلاميد']),
    //             'medicine_description' => json_encode(['en' => 'Sulfonylurea that stimulates insulin secretion.', 'ar' => 'سلفونيل يوريا يحفز إفراز الأنسولين.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 2.5-20mg once daily before breakfast.', 'ar' => 'يؤخذ 2.5-20 مجم مرة واحدة يومياً قبل الإفطار.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Hypoglycemia, weight gain, nausea.', 'ar' => 'انخفاض السكر، زيادة الوزن، غثيان.']),
    //             'medicine_slug' => Str::slug('Glibenclamide'),
    //             'medicine_price' => 15.00,
    //         ],
    //         [
    //             'category_id' => 4,
    //             'medicine_name' => json_encode(['en' => 'Insulin Glargine', 'ar' => 'أنسولين جلارجين']),
    //             'medicine_description' => json_encode(['en' => 'Long-acting insulin for type 1 and type 2 diabetes.', 'ar' => 'أنسولين طويل المفعول لعلاج السكري من النوع الأول والثاني.']),
    //             'medicine_usage' => json_encode(['en' => 'Inject subcutaneously once daily at the same time.', 'ar' => 'يحقن تحت الجلد مرة واحدة يومياً في نفس الوقت.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Hypoglycemia, injection site reactions.', 'ar' => 'انخفاض السكر، تفاعلات في موقع الحقن.']),
    //             'medicine_slug' => Str::slug('Insulin Glargine'),
    //             'medicine_price' => 180.00,
    //         ],
    //         [
    //             'category_id' => 4,
    //             'medicine_name' => json_encode(['en' => 'Sitagliptin', 'ar' => 'سيتاغليبتين']),
    //             'medicine_description' => json_encode(['en' => 'DPP-4 inhibitor for blood sugar control in type 2 diabetes.', 'ar' => 'مثبط DPP-4 للتحكم في سكر الدم في السكري من النوع الثاني.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 100mg once daily with or without food.', 'ar' => 'يؤخذ 100 مجم مرة واحدة يومياً مع أو بدون طعام.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Runny nose, sore throat, upper respiratory infection.', 'ar' => 'سيلان الأنف، ألم الحلق، التهاب الجهاز التنفسي العلوي.']),
    //             'medicine_slug' => Str::slug('Sitagliptin'),
    //             'medicine_price' => 220.00,
    //         ],
    //         [
    //             'category_id' => 4,
    //             'medicine_name' => json_encode(['en' => 'Empagliflozin', 'ar' => 'إمباغليفلوزين']),
    //             'medicine_description' => json_encode(['en' => 'SGLT2 inhibitor reducing blood sugar and cardiovascular risk.', 'ar' => 'مثبط SGLT2 يخفض سكر الدم وخطر القلب والأوعية الدموية.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 10-25mg once daily in the morning.', 'ar' => 'يؤخذ 10-25 مجم مرة واحدة يومياً في الصباح.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Urinary tract infections, genital infections.', 'ar' => 'التهابات المسالك البولية، التهابات تناسلية.']),
    //             'medicine_slug' => Str::slug('Empagliflozin'),
    //             'medicine_price' => 260.00,
    //         ],

    //         // Category 5 - Respiratory
    //         [
    //             'category_id' => 5,
    //             'medicine_name' => json_encode(['en' => 'Salbutamol', 'ar' => 'سالبوتامول']),
    //             'medicine_description' => json_encode(['en' => 'Bronchodilator for relief of bronchospasm in asthma.', 'ar' => 'موسع للشعب الهوائية لتخفيف تشنج الشعب في الربو.']),
    //             'medicine_usage' => json_encode(['en' => 'Inhale 1-2 puffs every 4-6 hours as needed.', 'ar' => 'يستنشق 1-2 بخة كل 4-6 ساعات حسب الحاجة.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Tremor, palpitations, headache.', 'ar' => 'رعشة، خفقان، صداع.']),
    //             'medicine_slug' => Str::slug('Salbutamol'),
    //             'medicine_price' => 45.00,
    //         ],
    //         [
    //             'category_id' => 5,
    //             'medicine_name' => json_encode(['en' => 'Budesonide', 'ar' => 'بوديزونيد']),
    //             'medicine_description' => json_encode(['en' => 'Inhaled corticosteroid for asthma and COPD management.', 'ar' => 'كورتيكوستيرويد يستنشق لعلاج الربو ومرض الانسداد الرئوي المزمن.']),
    //             'medicine_usage' => json_encode(['en' => 'Inhale 200-400mcg twice daily.', 'ar' => 'يستنشق 200-400 ميكروغرام مرتين يومياً.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Oral thrush, hoarseness, cough.', 'ar' => 'قلاع فموي، بحة، سعال.']),
    //             'medicine_slug' => Str::slug('Budesonide'),
    //             'medicine_price' => 120.00,
    //         ],
    //         [
    //             'category_id' => 5,
    //             'medicine_name' => json_encode(['en' => 'Montelukast', 'ar' => 'مونتيلوكاست']),
    //             'medicine_description' => json_encode(['en' => 'Leukotriene receptor antagonist for asthma and allergic rhinitis.', 'ar' => 'مضاد لمستقبلات الليكوترين لعلاج الربو والتهاب الأنف التحسسي.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 10mg once daily in the evening.', 'ar' => 'يؤخذ 10 مجم مرة واحدة يومياً في المساء.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Headache, stomach pain, mood changes.', 'ar' => 'صداع، ألم في المعدة، تغيرات مزاجية.']),
    //             'medicine_slug' => Str::slug('Montelukast'),
    //             'medicine_price' => 85.00,
    //         ],
    //         [
    //             'category_id' => 5,
    //             'medicine_name' => json_encode(['en' => 'Cetirizine', 'ar' => 'سيتيريزين']),
    //             'medicine_description' => json_encode(['en' => 'Antihistamine for allergies, hay fever, and urticaria.', 'ar' => 'مضاد هيستامين للحساسية وحمى القش والشرى.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 10mg once daily.', 'ar' => 'يؤخذ 10 مجم مرة واحدة يومياً.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Drowsiness, dry mouth, headache.', 'ar' => 'نعاس، جفاف الفم، صداع.']),
    //             'medicine_slug' => Str::slug('Cetirizine'),
    //             'medicine_price' => 20.00,
    //         ],
    //         [
    //             'category_id' => 5,
    //             'medicine_name' => json_encode(['en' => 'Dextromethorphan', 'ar' => 'ديكستروميثورفان']),
    //             'medicine_description' => json_encode(['en' => 'Cough suppressant for non-productive cough.', 'ar' => 'مثبط للسعال للسعال غير المنتج.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 10-20mg every 4 hours as needed.', 'ar' => 'يؤخذ 10-20 مجم كل 4 ساعات حسب الحاجة.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Drowsiness, dizziness, nausea.', 'ar' => 'نعاس، دوخة، غثيان.']),
    //             'medicine_slug' => Str::slug('Dextromethorphan'),
    //             'medicine_price' => 30.00,
    //         ],

    //         // Category 6 - Gastrointestinal
    //         [
    //             'category_id' => 6,
    //             'medicine_name' => json_encode(['en' => 'Omeprazole', 'ar' => 'أوميبرازول']),
    //             'medicine_description' => json_encode(['en' => 'Proton pump inhibitor for acid reflux and ulcers.', 'ar' => 'مثبط مضخة البروتون لعلاج ارتداد الحمض والقرحة.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 20-40mg once daily before a meal.', 'ar' => 'يؤخذ 20-40 مجم مرة واحدة يومياً قبل الوجبة.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Headache, diarrhea, stomach pain.', 'ar' => 'صداع، إسهال، ألم في المعدة.']),
    //             'medicine_slug' => Str::slug('Omeprazole'),
    //             'medicine_price' => 38.00,
    //         ],
    //         [
    //             'category_id' => 6,
    //             'medicine_name' => json_encode(['en' => 'Domperidone', 'ar' => 'دومبيريدون']),
    //             'medicine_description' => json_encode(['en' => 'Antiemetic to relieve nausea and vomiting.', 'ar' => 'مضاد للقيء لتخفيف الغثيان والقيء.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 10mg up to three times daily before meals.', 'ar' => 'يؤخذ 10 مجم حتى ثلاث مرات يومياً قبل الوجبات.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Dry mouth, headache, diarrhea.', 'ar' => 'جفاف الفم، صداع، إسهال.']),
    //             'medicine_slug' => Str::slug('Domperidone'),
    //             'medicine_price' => 25.00,
    //         ],
    //         [
    //             'category_id' => 6,
    //             'medicine_name' => json_encode(['en' => 'Loperamide', 'ar' => 'لوبيراميد']),
    //             'medicine_description' => json_encode(['en' => 'Anti-diarrheal medication to slow gut movement.', 'ar' => 'دواء مضاد للإسهال يبطئ حركة الأمعاء.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 4mg initially then 2mg after each loose stool.', 'ar' => 'يؤخذ 4 مجم في البداية ثم 2 مجم بعد كل براز رخو.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Constipation, bloating, dizziness.', 'ar' => 'إمساك، انتفاخ، دوخة.']),
    //             'medicine_slug' => Str::slug('Loperamide'),
    //             'medicine_price' => 18.00,
    //         ],
    //         [
    //             'category_id' => 6,
    //             'medicine_name' => json_encode(['en' => 'Ranitidine', 'ar' => 'رانيتيدين']),
    //             'medicine_description' => json_encode(['en' => 'H2 blocker to reduce stomach acid production.', 'ar' => 'حاصر H2 لتقليل إنتاج حمض المعدة.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 150mg twice daily or 300mg at bedtime.', 'ar' => 'يؤخذ 150 مجم مرتين يومياً أو 300 مجم عند النوم.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Headache, dizziness, constipation.', 'ar' => 'صداع، دوخة، إمساك.']),
    //             'medicine_slug' => Str::slug('Ranitidine'),
    //             'medicine_price' => 22.00,
    //         ],
    //         [
    //             'category_id' => 6,
    //             'medicine_name' => json_encode(['en' => 'Lactulose', 'ar' => 'لاكتولوز']),
    //             'medicine_description' => json_encode(['en' => 'Osmotic laxative for constipation and hepatic encephalopathy.', 'ar' => 'ملين أوزموزي للإمساك والاعتلال الدماغي الكبدي.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 15-30ml once or twice daily adjusted to produce 2-3 soft stools.', 'ar' => 'يؤخذ 15-30 مل مرة أو مرتين يومياً بحسب الاستجابة.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Bloating, flatulence, diarrhea.', 'ar' => 'انتفاخ، غازات، إسهال.']),
    //             'medicine_slug' => Str::slug('Lactulose'),
    //             'medicine_price' => 30.00,
    //         ],

    //         // Category 7 - Neurological / CNS
    //         [
    //             'category_id' => 7,
    //             'medicine_name' => json_encode(['en' => 'Sertraline', 'ar' => 'سيرترالين']),
    //             'medicine_description' => json_encode(['en' => 'SSRI antidepressant for depression and anxiety disorders.', 'ar' => 'مضاد اكتئاب SSRI للاكتئاب واضطرابات القلق.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 50-200mg once daily with or without food.', 'ar' => 'يؤخذ 50-200 مجم مرة واحدة يومياً مع أو بدون طعام.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Nausea, insomnia, dry mouth, sexual dysfunction.', 'ar' => 'غثيان، أرق، جفاف الفم، خلل جنسي.']),
    //             'medicine_slug' => Str::slug('Sertraline'),
    //             'medicine_price' => 95.00,
    //         ],
    //         [
    //             'category_id' => 7,
    //             'medicine_name' => json_encode(['en' => 'Diazepam', 'ar' => 'ديازيبام']),
    //             'medicine_description' => json_encode(['en' => 'Benzodiazepine for anxiety, seizures, and muscle spasm.', 'ar' => 'بنزوديازيبين للقلق والنوبات والتشنج العضلي.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 2-10mg 2-4 times daily as directed.', 'ar' => 'يؤخذ 2-10 مجم 2-4 مرات يومياً حسب التوجيه.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Drowsiness, confusion, dependence.', 'ar' => 'نعاس، ارتباك، الإدمان.']),
    //             'medicine_slug' => Str::slug('Diazepam'),
    //             'medicine_price' => 15.00,
    //         ],
    //         [
    //             'category_id' => 7,
    //             'medicine_name' => json_encode(['en' => 'Levodopa', 'ar' => 'ليفودوبا']),
    //             'medicine_description' => json_encode(['en' => 'Primary treatment for Parkinson\'s disease symptoms.', 'ar' => 'العلاج الأساسي لأعراض مرض باركنسون.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 100-300mg 3-4 times daily before meals.', 'ar' => 'يؤخذ 100-300 مجم 3-4 مرات يومياً قبل الوجبات.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Nausea, dyskinesia, orthostatic hypotension.', 'ar' => 'غثيان، خلل الحركة، انخفاض ضغط الدم الانتصابي.']),
    //             'medicine_slug' => Str::slug('Levodopa'),
    //             'medicine_price' => 110.00,
    //         ],
    //         [
    //             'category_id' => 7,
    //             'medicine_name' => json_encode(['en' => 'Phenytoin', 'ar' => 'فينيتوين']),
    //             'medicine_description' => json_encode(['en' => 'Anticonvulsant for epilepsy and seizure prevention.', 'ar' => 'مضاد التشنج لعلاج الصرع والوقاية من النوبات.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 100mg three times daily, adjusted per blood levels.', 'ar' => 'يؤخذ 100 مجم ثلاث مرات يومياً، يُعدَّل حسب مستويات الدم.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Gum overgrowth, diplopia, ataxia.', 'ar' => 'تضخم اللثة، ازدواج الرؤية، رنح.']),
    //             'medicine_slug' => Str::slug('Phenytoin'),
    //             'medicine_price' => 40.00,
    //         ],
    //         [
    //             'category_id' => 7,
    //             'medicine_name' => json_encode(['en' => 'Sumatriptan', 'ar' => 'سوماتريبتان']),
    //             'medicine_description' => json_encode(['en' => 'Triptan for acute migraine and cluster headache relief.', 'ar' => 'تريبتان لتخفيف الصداع النصفي الحاد والصداع العنقودي.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 50-100mg at onset of migraine; may repeat after 2 hours.', 'ar' => 'يؤخذ 50-100 مجم عند بداية الصداع النصفي؛ يمكن تكراره بعد ساعتين.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Chest tightness, flushing, dizziness.', 'ar' => 'ضيق في الصدر، احمرار، دوخة.']),
    //             'medicine_slug' => Str::slug('Sumatriptan'),
    //             'medicine_price' => 135.00,
    //         ],

    //         // Category 8 - Dermatology
    //         [
    //             'category_id' => 8,
    //             'medicine_name' => json_encode(['en' => 'Hydrocortisone Cream', 'ar' => 'كريم هيدروكورتيزون']),
    //             'medicine_description' => json_encode(['en' => 'Topical corticosteroid for skin inflammation and itching.', 'ar' => 'كورتيكوستيرويد موضعي لعلاج التهاب الجلد والحكة.']),
    //             'medicine_usage' => json_encode(['en' => 'Apply a thin layer to the affected area 2-3 times daily.', 'ar' => 'يُطبَّق طبقة رقيقة على المنطقة المصابة 2-3 مرات يومياً.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Skin thinning, burning, irritation with prolonged use.', 'ar' => 'ترقق الجلد، حرقان، تهيج عند الاستخدام المطول.']),
    //             'medicine_slug' => Str::slug('Hydrocortisone Cream'),
    //             'medicine_price' => 22.00,
    //         ],
    //         [
    //             'category_id' => 8,
    //             'medicine_name' => json_encode(['en' => 'Clotrimazole', 'ar' => 'كلوتريمازول']),
    //             'medicine_description' => json_encode(['en' => 'Antifungal for skin, vaginal, and oral fungal infections.', 'ar' => 'مضاد فطريات لعلاج عدوى الجلد والمهبل والفم.']),
    //             'medicine_usage' => json_encode(['en' => 'Apply to affected skin twice daily for 2-4 weeks.', 'ar' => 'يُطبَّق على الجلد المصاب مرتين يومياً لمدة 2-4 أسابيع.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Burning sensation, redness, skin irritation.', 'ar' => 'إحساس بالحرقان، احمرار، تهيج جلدي.']),
    //             'medicine_slug' => Str::slug('Clotrimazole'),
    //             'medicine_price' => 28.00,
    //         ],
    //         [
    //             'category_id' => 8,
    //             'medicine_name' => json_encode(['en' => 'Tretinoin', 'ar' => 'تريتينوين']),
    //             'medicine_description' => json_encode(['en' => 'Vitamin A derivative for acne and skin aging.', 'ar' => 'مشتق من فيتامين A لعلاج حب الشباب وشيخوخة الجلد.']),
    //             'medicine_usage' => json_encode(['en' => 'Apply a pea-sized amount at night to clean skin.', 'ar' => 'يُطبَّق كمية بحجم حبة البازلاء ليلاً على الجلد النظيف.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Skin dryness, redness, peeling, photosensitivity.', 'ar' => 'جفاف الجلد، احمرار، تقشر، حساسية للضوء.']),
    //             'medicine_slug' => Str::slug('Tretinoin'),
    //             'medicine_price' => 75.00,
    //         ],
    //         [
    //             'category_id' => 8,
    //             'medicine_name' => json_encode(['en' => 'Permethrin', 'ar' => 'بيرميثرين']),
    //             'medicine_description' => json_encode(['en' => 'Topical treatment for scabies and head lice.', 'ar' => 'علاج موضعي للجرب وقمل الرأس.']),
    //             'medicine_usage' => json_encode(['en' => 'Apply cream over entire body, wash off after 8-14 hours.', 'ar' => 'يُطبَّق الكريم على الجسم كله، يُغسل بعد 8-14 ساعة.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Mild burning, stinging, or numbness.', 'ar' => 'حرقان خفيف، وخز، أو تخدر.']),
    //             'medicine_slug' => Str::slug('Permethrin'),
    //             'medicine_price' => 35.00,
    //         ],
    //         [
    //             'category_id' => 8,
    //             'medicine_name' => json_encode(['en' => 'Betamethasone', 'ar' => 'بيتاميثازون']),
    //             'medicine_description' => json_encode(['en' => 'Potent topical corticosteroid for inflammatory skin conditions.', 'ar' => 'كورتيكوستيرويد موضعي قوي للحالات الجلدية الالتهابية.']),
    //             'medicine_usage' => json_encode(['en' => 'Apply a thin film to affected area once or twice daily.', 'ar' => 'يُطبَّق طبقة رقيقة على المنطقة المصابة مرة أو مرتين يومياً.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Skin atrophy, striae, telangiectasia.', 'ar' => 'ضمور الجلد، علامات التمدد، توسع الشعيرات.']),
    //             'medicine_slug' => Str::slug('Betamethasone'),
    //             'medicine_price' => 32.00,
    //         ],

    //         // Category 9 - Vitamins & Supplements
    //         [
    //             'category_id' => 9,
    //             'medicine_name' => json_encode(['en' => 'Vitamin D3', 'ar' => 'فيتامين د3']),
    //             'medicine_description' => json_encode(['en' => 'Essential vitamin for bone health and immune function.', 'ar' => 'فيتامين أساسي لصحة العظام ووظيفة المناعة.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 1000-4000 IU once daily with a fatty meal.', 'ar' => 'يؤخذ 1000-4000 وحدة دولية مرة واحدة يومياً مع وجبة دهنية.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Rare at recommended doses; toxicity with excessive intake.', 'ar' => 'نادرة بالجرعات الموصى بها؛ سمية عند الإفراط في تناوله.']),
    //             'medicine_slug' => Str::slug('Vitamin D3'),
    //             'medicine_price' => 48.00,
    //         ],
    //         [
    //             'category_id' => 9,
    //             'medicine_name' => json_encode(['en' => 'Folic Acid', 'ar' => 'حمض الفوليك']),
    //             'medicine_description' => json_encode(['en' => 'B vitamin essential for cell growth and pregnancy.', 'ar' => 'فيتامين B أساسي لنمو الخلايا والحمل.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 400-800mcg daily; 5mg daily during pregnancy if indicated.', 'ar' => 'يؤخذ 400-800 ميكروغرام يومياً؛ 5 مجم يومياً خلال الحمل إذا أُشير إليه.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Very rare at normal doses.', 'ar' => 'نادرة جداً بالجرعات الطبيعية.']),
    //             'medicine_slug' => Str::slug('Folic Acid'),
    //             'medicine_price' => 12.00,
    //         ],
    //         [
    //             'category_id' => 9,
    //             'medicine_name' => json_encode(['en' => 'Iron Supplement', 'ar' => 'مكمل الحديد']),
    //             'medicine_description' => json_encode(['en' => 'Ferrous sulfate for iron deficiency anemia treatment.', 'ar' => 'كبريتات الحديدوز لعلاج فقر الدم بنقص الحديد.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 200mg 1-3 times daily on an empty stomach.', 'ar' => 'يؤخذ 200 مجم 1-3 مرات يومياً على معدة فارغة.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Nausea, constipation, dark stools.', 'ar' => 'غثيان، إمساك، براز داكن.']),
    //             'medicine_slug' => Str::slug('Iron Supplement'),
    //             'medicine_price' => 18.00,
    //         ],
    //         [
    //             'category_id' => 9,
    //             'medicine_name' => json_encode(['en' => 'Calcium Carbonate', 'ar' => 'كربونات الكالسيوم']),
    //             'medicine_description' => json_encode(['en' => 'Calcium supplement for bone health and acid indigestion.', 'ar' => 'مكمل كالسيوم لصحة العظام وعسر الهضم الحمضي.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 500-1000mg twice daily with food.', 'ar' => 'يؤخذ 500-1000 مجم مرتين يومياً مع الطعام.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Constipation, bloating, gas.', 'ar' => 'إمساك، انتفاخ، غازات.']),
    //             'medicine_slug' => Str::slug('Calcium Carbonate'),
    //             'medicine_price' => 25.00,
    //         ],
    //         [
    //             'category_id' => 9,
    //             'medicine_name' => json_encode(['en' => 'Omega-3 Fish Oil', 'ar' => 'زيت سمك أوميغا-3']),
    //             'medicine_description' => json_encode(['en' => 'Omega-3 fatty acid supplement for heart and brain health.', 'ar' => 'مكمل أحماض أوميغا-3 الدهنية لصحة القلب والدماغ.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 1-3 capsules daily with meals.', 'ar' => 'يؤخذ 1-3 كبسولات يومياً مع الوجبات.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Fishy breath, nausea, loose stools.', 'ar' => 'رائحة السمك في الفم، غثيان، براز رخو.']),
    //             'medicine_slug' => Str::slug('Omega-3 Fish Oil'),
    //             'medicine_price' => 60.00,
    //         ],

    //         // Category 10 - Hormonal / Endocrine
    //         [
    //             'category_id' => 10,
    //             'medicine_name' => json_encode(['en' => 'Levothyroxine', 'ar' => 'ليفوثيروكسين']),
    //             'medicine_description' => json_encode(['en' => 'Synthetic thyroid hormone for hypothyroidism treatment.', 'ar' => 'هرمون الغدة الدرقية الاصطناعي لعلاج قصور الغدة الدرقية.']),
    //             'medicine_usage' => json_encode(['en' => 'Take once daily on an empty stomach 30-60 min before breakfast.', 'ar' => 'يؤخذ مرة واحدة يومياً على معدة فارغة قبل الإفطار بـ30-60 دقيقة.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Palpitations, weight loss, tremors if overdosed.', 'ar' => 'خفقان، فقدان الوزن، رعاش عند الجرعة الزائدة.']),
    //             'medicine_slug' => Str::slug('Levothyroxine'),
    //             'medicine_price' => 42.00,
    //         ],
    //         [
    //             'category_id' => 10,
    //             'medicine_name' => json_encode(['en' => 'Prednisolone', 'ar' => 'بريدنيزولون']),
    //             'medicine_description' => json_encode(['en' => 'Oral corticosteroid for inflammatory and immune conditions.', 'ar' => 'كورتيكوستيرويد فموي للحالات الالتهابية والمناعية.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 5-60mg daily as a single dose in the morning.', 'ar' => 'يؤخذ 5-60 مجم يومياً كجرعة واحدة في الصباح.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Weight gain, hyperglycemia, osteoporosis.', 'ar' => 'زيادة الوزن، ارتفاع السكر، هشاشة العظام.']),
    //             'medicine_slug' => Str::slug('Prednisolone'),
    //             'medicine_price' => 20.00,
    //         ],
    //         [
    //             'category_id' => 10,
    //             'medicine_name' => json_encode(['en' => 'Estradiol', 'ar' => 'إستراديول']),
    //             'medicine_description' => json_encode(['en' => 'Estrogen hormone for menopause and hormone replacement therapy.', 'ar' => 'هرمون الاستروجين لعلاج سن اليأس والعلاج بالهرمونات البديلة.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 1-2mg orally once daily or as directed.', 'ar' => 'يؤخذ 1-2 مجم فموياً مرة واحدة يومياً أو حسب التوجيه.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Nausea, breast tenderness, headache.', 'ar' => 'غثيان، ألم الثدي، صداع.']),
    //             'medicine_slug' => Str::slug('Estradiol'),
    //             'medicine_price' => 95.00,
    //         ],
    //         [
    //             'category_id' => 10,
    //             'medicine_name' => json_encode(['en' => 'Testosterone', 'ar' => 'تستوستيرون']),
    //             'medicine_description' => json_encode(['en' => 'Male hormone replacement for hypogonadism.', 'ar' => 'هرمون ذكوري بديل لعلاج قصور الغدد التناسلية.']),
    //             'medicine_usage' => json_encode(['en' => 'Apply gel daily or inject as directed by physician.', 'ar' => 'يُطبَّق الجل يومياً أو يُحقن حسب تعليمات الطبيب.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Acne, mood changes, increased red blood cells.', 'ar' => 'حب الشباب، تغيرات مزاجية، زيادة خلايا الدم الحمراء.']),
    //             'medicine_slug' => Str::slug('Testosterone'),
    //             'medicine_price' => 150.00,
    //         ],
    //         [
    //             'category_id' => 10,
    //             'medicine_name' => json_encode(['en' => 'Dexamethasone', 'ar' => 'ديكساميثازون']),
    //             'medicine_description' => json_encode(['en' => 'Potent corticosteroid for severe inflammation and immune suppression.', 'ar' => 'كورتيكوستيرويد قوي للالتهاب الشديد وقمع المناعة.']),
    //             'medicine_usage' => json_encode(['en' => 'Take 0.5-10mg daily depending on condition.', 'ar' => 'يؤخذ 0.5-10 مجم يومياً حسب الحالة.']),
    //             'medicine_side_effects' => json_encode(['en' => 'Insomnia, mood swings, increased appetite.', 'ar' => 'أرق، تقلبات مزاجية، زيادة الشهية.']),
    //             'medicine_slug' => Str::slug('Dexamethasone'),
    //             'medicine_price' => 18.00,
    //         ],
    //     ];

    //     // Add timestamps to all entries
    //     $medicines = array_map(function ($medicine) {
    //         return array_merge($medicine, [
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //     }, $medicines);

    //     DB::table('medicines')->insert($medicines);
    // }
        public function run(): void
    {
        $medicines = [
            // Category 1 - Pain Relievers / Analgesics
            [
                'category_id' => 1,
                'medicine_name' => json_encode(['en' => 'Panadol Advance 500mg', 'ar' => 'بانادول أدفانس 500 مجم']),
                'medicine_description' => json_encode(['en' => 'Paracetamol-based pain reliever and fever reducer.', 'ar' => 'مسكن للألم وخافض للحرارة يحتوي على الباراسيتامول.']),
                'medicine_usage' => json_encode(['en' => 'Take 1-2 tablets every 4-6 hours. Max 8 tablets/day.', 'ar' => 'يؤخذ 1-2 قرص كل 4-6 ساعات. بحد أقصى 8 أقراص يومياً.']),
                'medicine_side_effects' => json_encode(['en' => 'Rare, but overdosage can cause liver damage.', 'ar' => 'نادرة، ولكن الجرعات الزائدة قد تسبب تلف الكبد.']),
                'medicine_slug' => Str::slug('Panadol Advance 500mg'),
                'medicine_price' => 35.00,
            ],
            [
                'category_id' => 1,
                'medicine_name' => json_encode(['en' => 'Brufen 400mg', 'ar' => 'بروفين 400 مجم']),
                'medicine_description' => json_encode(['en' => 'Ibuprofen-based anti-inflammatory for pain and swelling.', 'ar' => 'مضاد للالتهابات ومسكن للألم والتورم يحتوي على إيبوبروفين.']),
                'medicine_usage' => json_encode(['en' => 'Take 1 tablet every 8 hours after food.', 'ar' => 'يؤخذ قرص واحد كل 8 ساعات بعد الأكل.']),
                'medicine_side_effects' => json_encode(['en' => 'Stomach upset, heartburn, nausea.', 'ar' => 'اضطراب المعدة، حرقة، غثيان.']),
                'medicine_slug' => Str::slug('Brufen 400mg'),
                'medicine_price' => 45.00,
            ],
            [
                'category_id' => 1,
                'medicine_name' => json_encode(['en' => 'Cataflam 50mg', 'ar' => 'كاتافلام 50 مجم']),
                'medicine_description' => json_encode(['en' => 'Diclofenac Potassium for rapid relief of acute pain.', 'ar' => 'ديكلوفيناك البوتاسيوم لتخفيف سريع للألم الحاد.']),
                'medicine_usage' => json_encode(['en' => 'Take 1 tablet 2-3 times daily after meals.', 'ar' => 'يؤخذ قرص 2-3 مرات يومياً بعد الوجبات.']),
                'medicine_side_effects' => json_encode(['en' => 'Indigestion, stomach pain, dizziness.', 'ar' => 'عسر الهضم، ألم المعدة، دوخة.']),
                'medicine_slug' => Str::slug('Cataflam 50mg'),
                'medicine_price' => 51.00,
            ],

            // Category 2 - Antibiotics
            [
                'category_id' => 2,
                'medicine_name' => json_encode(['en' => 'Augmentin 1g', 'ar' => 'أوجمنتين 1 جم']),
                'medicine_description' => json_encode(['en' => 'Broad-spectrum antibiotic (Amoxicillin + Clavulanate).', 'ar' => 'مضاد حيوي واسع المجال (أموكسيسيلين + كلافولانات).']),
                'medicine_usage' => json_encode(['en' => 'Take 1 tablet every 12 hours for 5-7 days.', 'ar' => 'يؤخذ قرص كل 12 ساعة لمدة 5-7 أيام.']),
                'medicine_side_effects' => json_encode(['en' => 'Diarrhea, yeast infections, upset stomach.', 'ar' => 'إسهال، التهابات فطرية، اضطراب المعدة.']),
                'medicine_slug' => Str::slug('Augmentin 1g'),
                'medicine_price' => 131.00,
            ],
            [
                'category_id' => 2,
                'medicine_name' => json_encode(['en' => 'Zithromax 500mg', 'ar' => 'زيثراماكس 500 مجم']),
                'medicine_description' => json_encode(['en' => 'Azithromycin antibiotic for respiratory and skin infections.', 'ar' => 'مضاد حيوي أزيثرومايسين لعدوى الجهاز التنفسي والجلد.']),
                'medicine_usage' => json_encode(['en' => 'Take 1 tablet daily for 3 days.', 'ar' => 'يؤخذ قرص واحد يومياً لمدة 3 أيام.']),
                'medicine_side_effects' => json_encode(['en' => 'Nausea, abdominal pain, mild diarrhea.', 'ar' => 'غثيان، ألم في البطن، إسهال خفيف.']),
                'medicine_slug' => Str::slug('Zithromax 500mg'),
                'medicine_price' => 95.00,
            ],
            [
                'category_id' => 2,
                'medicine_name' => json_encode(['en' => 'Flagyl 500mg', 'ar' => 'فلاجيل 500 مجم']),
                'medicine_description' => json_encode(['en' => 'Metronidazole for anaerobic bacterial and parasitic infections.', 'ar' => 'ميترونيدازول للعدوى البكتيرية اللاهوائية والطفيلية.']),
                'medicine_usage' => json_encode(['en' => 'Take 1 tablet 3 times daily after food.', 'ar' => 'يؤخذ قرص 3 مرات يومياً بعد الأكل.']),
                'medicine_side_effects' => json_encode(['en' => 'Metallic taste, nausea, dark urine.', 'ar' => 'طعم معدني بالفم، غثيان، بول داكن.']),
                'medicine_slug' => Str::slug('Flagyl 500mg'),
                'medicine_price' => 40.00,
            ],

            // Category 3 - Cardiovascular
            [
                'category_id' => 3,
                'medicine_name' => json_encode(['en' => 'Lipitor 20mg', 'ar' => 'ليبيتور 20 مجم']),
                'medicine_description' => json_encode(['en' => 'Atorvastatin to lower cholesterol and triglycerides.', 'ar' => 'أتورفاستاتين لخفض الكوليسترول والدهون الثلاثية.']),
                'medicine_usage' => json_encode(['en' => 'Take 1 tablet daily, preferably in the evening.', 'ar' => 'يؤخذ قرص واحد يومياً، يفضل في المساء.']),
                'medicine_side_effects' => json_encode(['en' => 'Muscle and joint pain, mild nausea.', 'ar' => 'آلام العضلات والمفاصل، غثيان خفيف.']),
                'medicine_slug' => Str::slug('Lipitor 20mg'),
                'medicine_price' => 120.00,
            ],
            [
                'category_id' => 3,
                'medicine_name' => json_encode(['en' => 'Concor 5mg', 'ar' => 'كونكور 5 مجم']),
                'medicine_description' => json_encode(['en' => 'Bisoprolol beta-blocker for hypertension and heart conditions.', 'ar' => 'بيسوبرولول حاصر بيتا لعلاج ضغط الدم المرتفع وأمراض القلب.']),
                'medicine_usage' => json_encode(['en' => 'Take 1 tablet daily in the morning.', 'ar' => 'يؤخذ قرص واحد يومياً في الصباح.']),
                'medicine_side_effects' => json_encode(['en' => 'Fatigue, cold hands/feet, slow heartbeat.', 'ar' => 'إرهاق، برودة الأطراف، بطء ضربات القلب.']),
                'medicine_slug' => Str::slug('Concor 5mg'),
                'medicine_price' => 56.00,
            ],

            // Category 4 - Diabetes
            [
                'category_id' => 4,
                'medicine_name' => json_encode(['en' => 'Glucophage 1000mg', 'ar' => 'جلوكوفاج 1000 مجم']),
                'medicine_description' => json_encode(['en' => 'Metformin used to control high blood sugar in type 2 diabetes.', 'ar' => 'ميتفورمين للسيطرة على ارتفاع سكر الدم في السكري من النوع الثاني.']),
                'medicine_usage' => json_encode(['en' => 'Take 1 tablet twice daily with meals.', 'ar' => 'يؤخذ قرص مرتين يومياً مع الوجبات.']),
                'medicine_side_effects' => json_encode(['en' => 'Stomach upset, nausea, metallic taste.', 'ar' => 'اضطراب المعدة، غثيان، طعم معدني.']),
                'medicine_slug' => Str::slug('Glucophage 1000mg'),
                'medicine_price' => 60.00,
            ],
            [
                'category_id' => 4,
                'medicine_name' => json_encode(['en' => 'Lantus Solostar Pen', 'ar' => 'قلم لانتوس سولوستار']),
                'medicine_description' => json_encode(['en' => 'Long-acting insulin glargine for blood sugar control.', 'ar' => 'أنسولين جلارجين طويل المفعول للتحكم في سكر الدم.']),
                'medicine_usage' => json_encode(['en' => 'Inject once daily subcutaneously at the same time.', 'ar' => 'يحقن مرة واحدة يومياً تحت الجلد في نفس الوقت.']),
                'medicine_side_effects' => json_encode(['en' => 'Hypoglycemia, weight gain, injection site reactions.', 'ar' => 'انخفاض السكر، زيادة الوزن، احمرار موقع الحقن.']),
                'medicine_slug' => Str::slug('Lantus Solostar Pen'),
                'medicine_price' => 210.00,
            ],

            // Category 5 - Respiratory
            [
                'category_id' => 5,
                'medicine_name' => json_encode(['en' => 'Ventolin Inhaler', 'ar' => 'بخاخ فنتولين']),
                'medicine_description' => json_encode(['en' => 'Salbutamol rescue inhaler for asthma and COPD.', 'ar' => 'بخاخ سالبوتامول إسعافي للربو وانسداد الشعب الهوائية.']),
                'medicine_usage' => json_encode(['en' => 'Inhale 1-2 puffs every 4-6 hours as needed.', 'ar' => 'يستنشق 1-2 بخة كل 4-6 ساعات عند اللزوم.']),
                'medicine_side_effects' => json_encode(['en' => 'Shakiness, fast heartbeat, headache.', 'ar' => 'رعشة، تسارع ضربات القلب، صداع.']),
                'medicine_slug' => Str::slug('Ventolin Inhaler'),
                'medicine_price' => 75.00,
            ],
            [
                'category_id' => 5,
                'medicine_name' => json_encode(['en' => 'Zyrtec 10mg', 'ar' => 'زيرتيك 10 مجم']),
                'medicine_description' => json_encode(['en' => 'Cetirizine antihistamine for allergy relief.', 'ar' => 'سيتيريزين مضاد للحساسية والرشح.']),
                'medicine_usage' => json_encode(['en' => 'Take 1 tablet daily before bedtime.', 'ar' => 'يؤخذ قرص واحد يومياً قبل النوم.']),
                'medicine_side_effects' => json_encode(['en' => 'Drowsiness, dry mouth, fatigue.', 'ar' => 'نعاس، جفاف الفم، إرهاق.']),
                'medicine_slug' => Str::slug('Zyrtec 10mg'),
                'medicine_price' => 54.00,
            ],

            // Category 6 - Gastrointestinal
            [
                'category_id' => 6,
                'medicine_name' => json_encode(['en' => 'Nexium 40mg', 'ar' => 'نيكسيوم 40 مجم']),
                'medicine_description' => json_encode(['en' => 'Esomeprazole PPI for acid reflux and stomach ulcers.', 'ar' => 'إيسوميبرازول لعلاج ارتجاع المريء وقرحة المعدة.']),
                'medicine_usage' => json_encode(['en' => 'Take 1 tablet daily 30 mins before breakfast.', 'ar' => 'يؤخذ قرص يومياً قبل الإفطار بـ 30 دقيقة.']),
                'medicine_side_effects' => json_encode(['en' => 'Headache, stomach pain, gas.', 'ar' => 'صداع، ألم في المعدة، غازات.']),
                'medicine_slug' => Str::slug('Nexium 40mg'),
                'medicine_price' => 146.00,
            ],
            [
                'category_id' => 6,
                'medicine_name' => json_encode(['en' => 'Motilium 10mg', 'ar' => 'موتيليوم 10 مجم']),
                'medicine_description' => json_encode(['en' => 'Domperidone for treating nausea and vomiting.', 'ar' => 'دومبيريدون لعلاج الغثيان والقيء وتسهيل الهضم.']),
                'medicine_usage' => json_encode(['en' => 'Take 1 tablet 15-30 mins before meals.', 'ar' => 'يؤخذ قرص قبل الوجبات بـ 15-30 دقيقة.']),
                'medicine_side_effects' => json_encode(['en' => 'Dry mouth, drowsiness, headache.', 'ar' => 'جفاف الفم، نعاس، صداع.']),
                'medicine_slug' => Str::slug('Motilium 10mg'),
                'medicine_price' => 45.00,
            ],

            // Category 7 - Dermatology
            [
                'category_id' => 8,
                'medicine_name' => json_encode(['en' => 'Fucidin Cream 2%', 'ar' => 'كريم فيوسيدين 2%']),
                'medicine_description' => json_encode(['en' => 'Topical antibiotic for skin infections.', 'ar' => 'مضاد حيوي موضعي للالتهابات الجلدية.']),
                'medicine_usage' => json_encode(['en' => 'Apply to the affected area 2-3 times daily.', 'ar' => 'يُطبَّق على المنطقة المصابة 2-3 مرات يومياً.']),
                'medicine_side_effects' => json_encode(['en' => 'Mild skin irritation or itching.', 'ar' => 'تهيج جلدي خفيف أو حكة.']),
                'medicine_slug' => Str::slug('Fucidin Cream 2%'),
                'medicine_price' => 32.00,
            ],

            // Category 8 - Vitamins & Supplements
            [
                'category_id' => 9,
                'medicine_name' => json_encode(['en' => 'Ferrotron Capsules', 'ar' => 'كبسولات فيروترون']),
                'medicine_description' => json_encode(['en' => 'Iron supplement with vitamins for anemia.', 'ar' => 'مكمل حديد مدعم بالفيتامينات لعلاج فقر الدم.']),
                'medicine_usage' => json_encode(['en' => 'Take 1 capsule daily after a meal.', 'ar' => 'تؤخذ كبسولة واحدة يومياً بعد الأكل.']),
                'medicine_side_effects' => json_encode(['en' => 'Constipation, dark stools, nausea.', 'ar' => 'إمساك، براز داكن، غثيان.']),
                'medicine_slug' => Str::slug('Ferrotron Capsules'),
                'medicine_price' => 80.00,
            ],
            [
                'category_id' => 9,
                'medicine_name' => json_encode(['en' => 'Vidrop Drops', 'ar' => 'نقط فيدروب']),
                'medicine_description' => json_encode(['en' => 'Vitamin D3 drops for bone health and immunity.', 'ar' => 'نقط فيتامين د3 لصحة العظام والمناعة.']),
                'medicine_usage' => json_encode(['en' => 'Dosage varies by age; typically prescribed daily or weekly.', 'ar' => 'تختلف الجرعة حسب العمر؛ وتؤخذ بشكل يومي أو أسبوعي.']),
                'medicine_side_effects' => json_encode(['en' => 'Rare, excessive dose may cause hypercalcemia.', 'ar' => 'نادرة، الجرعة المفرطة قد تسبب زيادة الكالسيوم.']),
                'medicine_slug' => Str::slug('Vidrop Drops'),
                'medicine_price' => 20.00,
            ],

            // Category 9 - Hormonal / Endocrine
            [
                'category_id' => 10,
                'medicine_name' => json_encode(['en' => 'Eltroxin 50mcg', 'ar' => 'إلتروكسين 50 ميكروجرام']),
                'medicine_description' => json_encode(['en' => 'Levothyroxine for hypothyroidism management.', 'ar' => 'ليفوثيروكسين لعلاج قصور الغدة الدرقية.']),
                'medicine_usage' => json_encode(['en' => 'Take once daily on an empty stomach.', 'ar' => 'يؤخذ مرة واحدة يومياً على معدة فارغة.']),
                'medicine_side_effects' => json_encode(['en' => 'Palpitations, weight loss, tremors (if dose is high).', 'ar' => 'خفقان، فقدان الوزن، رعشة (في حال زيادة الجرعة).']),
                'medicine_slug' => Str::slug('Eltroxin 50mcg'),
                'medicine_price' => 45.00,
            ]
        ];

        // Add timestamps to all entries
        $medicines = array_map(function ($medicine) {
            return array_merge($medicine, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, $medicines);

        DB::table('medicines')->insert($medicines);
    }
}
