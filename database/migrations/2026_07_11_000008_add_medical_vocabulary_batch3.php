<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Batch 3 (final) of Medical vocabulary for lesson_id=6. Adds 112
     * more words, bringing the lesson to exactly 500 (89 original +
     * 149 batch1 + 150 batch2 + 112 batch3).
     */
    public function up(): void
    {
        $words = [
            ['Abdominal', 'উদরীয়', 'belly-related', 'none'],
            ['Acuity', 'তীক্ষ্ণতা (দৃষ্টি বা শ্রবণ)', 'sharpness, clarity', 'dullness'],
            ['Adverse', 'প্রতিকূল', 'harmful, negative', 'beneficial'],
            ['Aggravate', 'বাড়িয়ে দেওয়া', 'worsen, intensify', 'alleviate'],
            ['Alertness', 'সজাগতা', 'attentiveness, vigilance', 'drowsiness'],
            ['Alleviate', 'উপশম করা', 'relieve, ease', 'aggravate'],
            ['Ambidextrous', 'উভয়হস্তে দক্ষ', 'dual-handed', 'none'],
            ['Amputation', 'অঙ্গচ্ছেদ', 'limb removal', 'none'],
            ['Anatomical', 'দেহতাত্ত্বিক', 'structural, bodily', 'none'],
            ['Anesthesia', 'অবেদন', 'numbing, sedation', 'none'],
            ['Aneurysm', 'ধমনীর স্ফীতি', 'vessel bulge', 'none'],
            ['Ankle', 'গোড়ালি', 'joint above the foot', 'none'],
            ['Antiviral', 'ভাইরাসরোধী', 'virus-fighting', 'none'],
            ['Apathy', 'উদাসীনতা', 'indifference', 'enthusiasm'],
            ['Arachnoid', 'মাকড়সাজালির মতো ঝিল্লি', 'brain membrane layer', 'none'],
            ['Arterial', 'ধমনীসংক্রান্ত', 'artery-related', 'venous'],
            ['Arthroscopy', 'আর্থ্রোস্কোপি', 'joint examination', 'none'],
            ['Asepsis', 'জীবাণুমুক্ত অবস্থা', 'sterility', 'sepsis'],
            ['Auditory', 'শ্রবণসংক্রান্ত', 'hearing-related', 'none'],
            ['Autoimmune', 'স্বয়ংক্রিয় প্রতিরোধ ব্যাধি', 'self-attacking immune', 'none'],
            ['Axilla', 'বগল', 'armpit', 'none'],
            ['Bilateral', 'উভয়পার্শ্বীয়', 'two-sided', 'unilateral'],
            ['Bloating', 'পেট ফুলে যাওয়া', 'swelling, distension', 'none'],
            ['Cadaver', 'মৃতদেহ (চিকিৎসা শিক্ষার জন্য)', 'corpse', 'none'],
            ['Calcification', 'ক্যালসিয়াম জমা হওয়া', 'hardening', 'none'],
            ['Calorie', 'ক্যালরি', 'energy unit', 'none'],
            ['Cardiologist', 'হৃদরোগ বিশেষজ্ঞ', 'heart doctor', 'none'],
            ['Carpal', 'কব্জিসংক্রান্ত', 'wrist-related', 'none'],
            ['Cauterize', 'দগ্ধ করে চিকিৎসা করা', 'burn to seal', 'none'],
            ['Cephalic', 'মস্তকসংক্রান্ত', 'head-related', 'caudal'],
            ['Clavicle', 'কণ্ঠাস্থি', 'collarbone', 'none'],
            ['Coccyx', 'পুচ্ছাস্থি', 'tailbone', 'none'],
            ['Cognitive', 'জ্ঞানীয়', 'mental, intellectual', 'none'],
            ['Comatose', 'কোমাগ্রস্ত', 'unconscious', 'conscious'],
            ['Complication', 'জটিলতা', 'problem, issue', 'none'],
            ['Contagion', 'সংক্রমণ', 'transmission of disease', 'none'],
            ['Contraception', 'গর্ভনিরোধ', 'birth control', 'none'],
            ['Cortex', 'বহিরাবরণ (মস্তিষ্ক বা বৃক্ক)', 'outer layer', 'medulla'],
            ['Cranial', 'করোটিসংক্রান্ত', 'skull-related', 'none'],
            ['Cystic', 'সিস্টযুক্ত', 'sac-like', 'none'],
            ['Deafness', 'বধিরতা', 'hearing loss', 'none'],
            ['Debridement', 'মৃত টিস্যু অপসারণ', 'wound cleaning', 'none'],
            ['Decompression', 'চাপ কমানো', 'pressure relief', 'compression'],
            ['Deficient', 'ঘাটতিযুক্ত', 'lacking, insufficient', 'sufficient'],
            ['Depression', 'বিষণ্নতা', 'sadness, melancholy', 'joy, happiness'],
            ['Diagnostic', 'রোগনির্ণয়মূলক', 'identifying, analytical', 'none'],
            ['Digit', 'আঙুল', 'finger, toe', 'none'],
            ['Disorientation', 'দিশাহারা ভাব', 'confusion', 'clarity'],
            ['Distal', 'দূরবর্তী প্রান্তীয়', 'far end', 'proximal'],
            ['Diuretic', 'মূত্রবর্ধক', 'water pill', 'none'],
            ['Dorsal', 'পৃষ্ঠদেশীয়', 'back-related', 'ventral'],
            ['Dressing', 'ড্রেসিং', 'bandage', 'none'],
            ['Dyslexia', 'পঠন প্রতিবন্ধকতা', 'reading disorder', 'none'],
            ['Dysfunction', 'অকার্যকারিতা', 'malfunction, impairment', 'proper function'],
            ['Dyspepsia', 'বদহজম', 'indigestion', 'none'],
            ['Dyspnea', 'শ্বাসকষ্ট', 'breathlessness', 'easy breathing'],
            ['Edematous', 'শোথযুক্ত', 'swollen', 'none'],
            ['Elbow', 'কনুই', 'arm joint', 'none'],
            ['Electrocardiogram', 'ইসিজি', 'ECG, heart tracing', 'none'],
            ['Embryonic', 'ভ্রূণীয়', 'early developmental', 'none'],
            ['Encephalitis', 'মস্তিষ্কের প্রদাহ', 'brain inflammation', 'none'],
            ['Endocrinologist', 'হরমোন বিশেষজ্ঞ', 'hormone specialist', 'none'],
            ['Enteric', 'অন্ত্রসংক্রান্ত', 'intestinal', 'none'],
            ['Epidural', 'মেরুদণ্ডের বাইরে ইনজেকশন', 'spinal anesthesia type', 'none'],
            ['Esophageal', 'অন্ননালীসংক্রান্ত', 'gullet-related', 'none'],
            ['Etiology', 'রোগের কারণবিদ্যা', 'cause study', 'none'],
            ['Excise', 'কেটে বাদ দেওয়া', 'remove surgically', 'implant'],
            ['Expire', 'মৃত্যুবরণ করা', 'pass away, die', 'survive'],
            ['Extraction', 'নিষ্কাশন, উত্তোলন', 'removal, pulling out', 'insertion'],
            ['Femur', 'উরুর হাড়', 'thighbone', 'none'],
            ['Fibrosis', 'টিস্যু শক্ত হয়ে যাওয়া', 'scarring', 'none'],
            ['Fibula', 'বহিঃপায়ুস্থি', 'calf bone', 'none'],
            ['Forensic', 'ফরেনসিক', 'legal-medical', 'none'],
            ['Frostbite', 'তুষার ক্ষত', 'cold injury', 'none'],
            ['Gait', 'চলাফেরার ভঙ্গি', 'walking pattern', 'none'],
            ['Gangrene', 'পচন', 'tissue death', 'none'],
            ['Gauge', 'পরিমাপক', 'measure, size', 'none'],
            ['Glucose', 'গ্লুকোজ', 'blood sugar', 'none'],
            ['Goiter', 'গলগণ্ড', 'thyroid swelling', 'none'],
            ['Grimace', 'ব্যথায় মুখ বিকৃত করা', 'wince', 'smile'],
            ['Growth', 'বৃদ্ধি, টিউমার', 'mass, tumor', 'none'],
            ['Hallux', 'বৃদ্ধাঙ্গুলি (পায়ের)', 'big toe', 'none'],
            ['Hemiplegia', 'অর্ধাঙ্গ পক্ষাঘাত', 'one-side paralysis', 'none'],
            ['Hemophilia', 'রক্তক্ষরণ রোগ', 'bleeding disorder', 'none'],
            ['Hemostasis', 'রক্তক্ষরণ বন্ধ হওয়া', 'bleeding control', 'hemorrhage'],
            ['Humerus', 'বাহুর হাড়', 'upper arm bone', 'none'],
            ['Hydrate', 'জলসেচন করা', 'moisten, replenish fluids', 'dehydrate'],
            ['Hygienic', 'স্বাস্থ্যসম্মত', 'sanitary, clean', 'unhygienic'],
            ['Hyperglycemia', 'উচ্চ রক্তে শর্করা', 'high blood sugar', 'hypoglycemia'],
            ['Hypoglycemia', 'নিম্ন রক্তে শর্করা', 'low blood sugar', 'hyperglycemia'],
            ['Immunodeficiency', 'প্রতিরোধ ক্ষমতার ঘাটতি', 'weak immunity', 'none'],
            ['Incontinence', 'অসংযম (মূত্র বা মল)', 'lack of control', 'continence'],
            ['Infarction', 'রক্তসঞ্চালন বন্ধ হয়ে টিস্যু মৃত্যু', 'tissue death from blockage', 'none'],
            ['Infusion', 'শিরায় তরল প্রদান', 'IV drip', 'none'],
            ['Inhaler', 'ইনহেলার', 'puffer device', 'none'],
            ['Insomniac', 'অনিদ্রারোগী', 'sleepless person', 'none'],
            ['Intubation', 'শ্বাসনালীতে টিউব প্রবেশ', 'tube insertion', 'extubation'],
            ['Jugular', 'গলার শিরাসংক্রান্ত', 'neck vein related', 'none'],
            ['Keratin', 'কেরাটিন', 'skin or hair protein', 'none'],
            ['Lacerate', 'ছিঁড়ে ফেলা', 'cut, tear', 'heal'],
            ['Lactose', 'দুগ্ধশর্করা', 'milk sugar', 'none'],
            ['Malady', 'ব্যাধি', 'illness, disease', 'health'],
            ['Manifest', 'প্রকাশ পাওয়া', 'show, appear', 'hide'],
            ['Melanin', 'মেলানিন', 'skin pigment', 'none'],
            ['Meningitis', 'মেনিনজাইটিস', 'brain membrane infection', 'none'],
            ['Menopause', 'রজোনিবৃত্তি', 'end of menstruation', 'menarche'],
            ['Metabolic', 'বিপাকীয়', 'digestion-related', 'none'],
            ['Myopia', 'নিকটদৃষ্টি', 'nearsightedness', 'hyperopia'],
            ['Narcotic', 'মাদক, ঘুমপাড়ানি ঔষধ', 'opioid, sedative', 'stimulant'],
            ['Nutrition', 'পুষ্টি', 'nourishment, diet', 'malnutrition'],
            ['Obesity', 'স্থূলতা', 'overweight condition', 'leanness'],
            ['Osteoporosis', 'অস্থিক্ষয়', 'bone thinning', 'none'],
        ];

        $now = now();
        $rows = array_map(function ($w) use ($now) {
            return [
                'word' => $w[0],
                'meaning' => $w[1],
                'synonyms' => $w[2],
                'antonyms' => $w[3],
                'lesson_id' => 6,
                'status' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $words);

        foreach (array_chunk($rows, 50) as $chunk) {
            DB::table('words')->insert($chunk);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $words = [
            'Abdominal','Acuity','Adverse','Aggravate','Alertness','Alleviate','Ambidextrous','Amputation','Anatomical','Anesthesia',
            'Aneurysm','Ankle','Antiviral','Apathy','Arachnoid','Arterial','Arthroscopy','Asepsis','Auditory','Autoimmune',
            'Axilla','Bilateral','Bloating','Cadaver','Calcification','Calorie','Cardiologist','Carpal','Cauterize','Cephalic',
            'Clavicle','Coccyx','Cognitive','Comatose','Complication','Contagion','Contraception','Cortex','Cranial','Cystic',
            'Deafness','Debridement','Decompression','Deficient','Depression','Diagnostic','Digit','Disorientation','Distal','Diuretic',
            'Dorsal','Dressing','Dyslexia','Dysfunction','Dyspepsia','Dyspnea','Edematous','Elbow','Electrocardiogram','Embryonic',
            'Encephalitis','Endocrinologist','Enteric','Epidural','Esophageal','Etiology','Excise','Expire','Extraction','Femur',
            'Fibrosis','Fibula','Forensic','Frostbite','Gait','Gangrene','Gauge','Glucose','Goiter','Grimace',
            'Growth','Hallux','Hemiplegia','Hemophilia','Hemostasis','Humerus','Hydrate','Hygienic','Hyperglycemia','Hypoglycemia',
            'Immunodeficiency','Incontinence','Infarction','Infusion','Inhaler','Insomniac','Intubation','Jugular','Keratin','Lacerate',
            'Lactose','Malady','Manifest','Melanin','Meningitis','Menopause','Metabolic','Myopia','Narcotic','Nutrition',
            'Obesity','Osteoporosis',
        ];
        DB::table('words')->where('lesson_id', 6)->whereIn('word', $words)->delete();
    }
};
