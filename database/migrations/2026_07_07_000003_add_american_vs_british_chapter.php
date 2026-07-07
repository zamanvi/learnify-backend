<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Chapter
        $chapterId = DB::table('chapters')->insertGetId([
            'title'      => 'America 🇺🇸 vs Britain 🇬🇧 — একই অর্থ, কিন্তু ভিন্ন শব্দ!',
            'type'       => 'vocabulary',
            'image_path' => null,
            'status'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Lesson
        $lessonId = DB::table('lessons')->insertGetId([
            'title'      => 'America 🇺🇸 vs Britain 🇬🇧 — একই অর্থ, কিন্তু ভিন্ন শব্দ!',
            'type'       => 'american_british',
            'chapter_id' => $chapterId,
            'status'     => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Words  (synonyms = 🇺🇸 American,  antonyms = 🇬🇧 British)
        $words = [
            // ── Daily Life ──────────────────────────────────────────
            ['Flat',             'সমতল / বাসস্থান / চাপা টায়ার',        'apartment',                    'punctured tyre'],
            ['Apartment',        'বাসস্থান',                              'apartment',                    'flat'],
            ['Elevator',         'উপরে ওঠার যন্ত্র',                     'elevator',                     'lift'],
            ['Closet',           'পোশাক রাখার জায়গা',                   'closet',                       'wardrobe'],
            ['Yard',             'বাড়ির বাগান',                          'yard / backyard',              'garden'],
            ['Trash',            'আবর্জনা',                              'garbage / trash',              'rubbish'],
            ['Garbage can',      'ময়লার পাত্র',                         'garbage can',                  'dustbin / bin'],
            ['Dumpster',         'বড় ময়লার পাত্র',                      'dumpster',                     'skip'],
            ['Flashlight',       'টর্চ',                                  'flashlight',                   'torch'],
            ['Scotch tape',      'আঠালো টেপ',                            'scotch tape',                  'sellotape'],
            ['Thumbtack',        'পিন',                                   'thumbtack',                    'drawing pin'],
            ['Rubber band',      'রাবার ব্যান্ড',                        'rubber band',                  'elastic band'],
            ['Zipper',           'জিপার',                                 'zipper',                       'zip'],
            ['Eraser',           'মুছে ফেলার জিনিস',                    'eraser',                       'rubber'],
            ['Bulletin board',   'নোটিশ বোর্ড',                         'bulletin board',               'notice board'],
            ['Apartment building','বহুতল ভবন',                           'apartment building',           'block of flats'],
            ['First floor',      'তলা',                                   'ground floor',                 'first floor'],
            ['Downtown',         'শহরের কেন্দ্র',                        'downtown',                     'city centre'],
            ['Vacation home',    'ছুটির বাড়ি',                          'vacation home',                'holiday cottage'],

            // ── Food & Kitchen ──────────────────────────────────────
            ['Chips',            'ভাজা খাবার (crispy)',                  'potato chips (crispy)',        'crisps'],
            ['French fries',     'ভাজা আলু',                             'french fries',                 'chips'],
            ['Cookie',           'বেকারি খাবার',                         'cookie',                       'biscuit'],
            ['Biscuit',          'নরম রুটি জাতীয় খাবার',               'soft bread roll',              'cookie / cracker'],
            ['Candy',            'মিষ্টি খাবার',                         'candy / sweets',               'sweets'],
            ['Candy bar',        'চকলেট বার',                             'candy bar',                    'chocolate bar'],
            ['Soda',             'কার্বনেটেড পানীয়',                    'soda / soda pop',              'fizzy drink'],
            ['Cotton candy',     'তুলার মতো মিষ্টি',                    'cotton candy',                 'candyfloss'],
            ['Popsicle',         'বরফের মিষ্টি',                         'popsicle',                     'ice lolly'],
            ['Jello',            'জেলি জাতীয় খাবার',                   'jello',                        'jelly'],
            ['Jelly',            'ফলের সংরক্ষণ',                        'jelly (fruit preserve)',       'jam'],
            ['Eggplant',         'সবজি',                                  'eggplant',                     'aubergine'],
            ['Zucchini',         'সবজি',                                  'zucchini',                     'courgette'],
            ['Arugula',          'সবজি',                                  'arugula',                      'rocket'],
            ['Cilantro',         'মশলা',                                  'cilantro',                     'coriander'],
            ['Ground beef',      'মাংস (কিমা)',                          'ground beef',                  'minced meat'],
            ['Whole wheat',      'পুষ্টিকর আটার রুটি',                  'whole wheat bread',            'wholemeal bread'],
            ['Broil',            'রান্নার পদ্ধতি',                       'broil',                        'grill'],
            ['Stove',            'রান্নার চুলা',                         'stove',                        'cooker / hob'],
            ['Appetizer',        'প্রথম খাবার',                          'appetizer',                    'starter'],
            ['Entrée',           'মূল খাবার',                             'entrée (main course)',         'main course'],
            ['Dessert',          'মিষ্টান্ন',                             'dessert',                      'pudding / afters'],
            ['Takeout',          'বাইরে নিয়ে যাওয়া খাবার',             'takeout',                      'takeaway'],
            ['Dishtowel',        'থালা মোছার কাপড়',                    'dishtowel',                    'tea towel'],
            ['Canned food',      'টিনের খাবার',                          'canned food',                  'tinned food'],

            // ── Clothing ────────────────────────────────────────────
            ['Pants',            'পোশাক',                                 'trousers',                     'underpants'],
            ['Sneakers',         'জুতা',                                  'sneakers',                     'trainers'],
            ['Sweater',          'গরম পোশাক',                            'sweater',                      'jumper / pullover'],
            ['Vest',             'পোশাক',                                 'undershirt / vest',            'waistcoat'],
            ['Suspenders',       'পোশাকের অংশ (ট্রাউজার)',              'suspenders (trousers)',        'braces'],
            ['Tuxedo',           'আনুষ্ঠানিক পোশাক',                    'tuxedo',                       'dinner jacket'],
            ['Nightgown',        'রাতের পোশাক',                          'nightgown',                    'nightdress / nightie'],
            ['Bathrobe',         'গোসলের পোশাক',                         'bathrobe',                     'dressing gown'],
            ['Undershirt',       'ভেতরের পোশাক',                        'undershirt',                   'vest'],
            ['Barrette',         'চুলের ক্লিপ',                          'barrette',                     'hair clip / slide'],

            // ── Transportation ──────────────────────────────────────
            ['Truck',            'ভারী যানবাহন',                         'truck',                        'lorry'],
            ['Gas',              'জ্বালানি',                              'gas / petrol',                 'petrol'],
            ['Gas station',      'জ্বালানির স্টেশন',                    'gas station',                  'petrol station'],
            ['Hood',             'গাড়ির সামনের ঢাকনা',                 'car hood',                     'car bonnet'],
            ['Boot',             'গাড়ির পেছনের অংশ',                   'car trunk',                    'car boot'],
            ['Windshield',       'গাড়ির সামনের কাচ',                   'windshield',                   'windscreen'],
            ['Fender',           'গাড়ির পাশের অংশ',                    'fender',                       'bumper / wing'],
            ['Turn signal',      'গাড়ির দিক সংকেত',                    'turn signal',                  'indicator'],
            ['Muffler',          'গাড়ির শব্দ কমানোর যন্ত্র',           'muffler',                      'silencer'],
            ['Sedan',            'গাড়ির ধরন',                            'sedan',                        'saloon'],
            ['Station wagon',    'গাড়ির ধরন',                            'station wagon',                'estate car'],
            ['Freeway',          'দ্রুতগতির রাস্তা',                     'freeway / highway',            'motorway'],
            ['Highway',          'রাস্তা',                                'highway',                      'dual carriageway'],
            ['Overpass',         'উড়াল সড়ক',                            'overpass',                     'flyover'],
            ['Traffic circle',   'গোল চক্কর',                            'traffic circle',               'roundabout'],
            ['Intersection',     'রাস্তার মোড়',                         'intersection',                 'crossroads / junction'],
            ['Parking lot',      'গাড়ি রাখার জায়গা',                   'parking lot',                  'car park'],
            ['Parking garage',   'গাড়ি রাখার ঘর',                      'parking garage',               'multi-storey car park'],
            ['Crosswalk',        'পথচারী পারাপার',                       'crosswalk',                    'pedestrian crossing'],
            ['Cab',              'যানবাহন',                               'cab / taxi',                   'taxi'],
            ['Subway',           'ভূগর্ভস্থ রেলপথ',                     'subway / underground',         'underground / tube'],
            ['Airplane',         'বিমান',                                 'airplane',                     'aeroplane'],
            ['One-way ticket',   'একমুখী টিকিট',                        'one-way ticket',               'single ticket'],
            ['Round-trip',       'যাওয়া-আসার টিকিট',                   'round-trip ticket',            'return ticket'],
            ['Carry-on',         'হাতের লাগেজ',                          'carry-on bag',                 'hand luggage'],

            // ── Health ──────────────────────────────────────────────
            ['Band-Aid',         'ক্ষতের আবরণ',                         'band-aid',                     'plaster'],
            ['Tylenol',          'ব্যথার ওষুধ',                          'Tylenol / acetaminophen',      'paracetamol'],
            ['Shot',             'ইনজেকশন',                              'shot',                         'jab / injection'],
            ['Cotton swab',      'কানের পরিষ্কার',                       'cotton swab / Q-tip',          'cotton bud'],
            ['Emergency room',   'জরুরি চিকিৎসা কক্ষ',                 'emergency room (ER)',          'accident & emergency (A&E)'],
            ['Physician',        'ডাক্তার',                               'physician / doctor',           'doctor / GP'],
            ['Drugstore',        'ওষুধের দোকান',                        'drugstore',                    'chemist / pharmacy'],

            // ── Education ───────────────────────────────────────────
            ['Kindergarten',     'শিশু বিদ্যালয়',                       'kindergarten',                 'reception class'],
            ['Grades',           'পরীক্ষার ফলাফল',                      'grades',                       'marks'],
            ['Semester',         'শিক্ষার মেয়াদ',                       'semester',                     'term'],
            ['Principal',        'প্রধান শিক্ষক',                        'principal',                    'headmaster / headteacher'],
            ['Cafeteria',        'খাওয়ার ঘর',                            'cafeteria',                    'canteen'],
            ['Schedule',         'সময়সূচি',                              'schedule',                     'timetable'],
            ['Math',             'গণিত',                                  'math',                         'maths'],

            // ── Work & Finance ──────────────────────────────────────
            ['Resume',           'জীবনবৃত্তান্ত',                        'resume',                       'CV (curriculum vitae)'],
            ['Raise',            'বেতন বৃদ্ধি',                          'raise',                        'pay rise'],
            ['Checking account', 'ব্যাংক হিসাব',                        'checking account',             'current account'],
            ['Savings account',  'সঞ্চয় হিসাব',                        'savings account',              'deposit account'],
            ['ATM',              'টাকা তোলার যন্ত্র',                   'ATM / cash machine',           'cashpoint / cash machine'],
            ['Bill',             'টাকার নোট',                             'bill (paper money)',           'banknote'],
            ['Check',            'রেস্তোরাঁর বিল',                       'check',                        'bill'],
            ['Realtor',          'জমি / বাড়ি কারবারি',                  'realtor',                      'estate agent'],
            ['Attorney',         'আইনজীবী',                              'attorney / lawyer',            'solicitor / barrister'],

            // ── Communication ───────────────────────────────────────
            ['Cellphone',        'মোবাইল ফোন',                           'cellphone',                    'mobile phone'],
            ['Mail',             'চিঠি / বার্তা',                        'mail',                         'post'],
            ['Mailbox',          'চিঠির বাক্স',                          'mailbox',                      'postbox / letterbox'],
            ['Antenna',          'টেলিভিশনের যন্ত্র',                   'antenna',                      'aerial'],
            ['Zip code',         'এলাকার কোড',                           'zip code',                     'postcode'],

            // ── Spelling Differences ────────────────────────────────
            ['Color',            'রঙ',                                    'color',                        'colour'],
            ['Favorite',         'প্রিয়',                                 'favorite',                     'favourite'],
            ['Honor',            'সম্মান',                                'honor',                        'honour'],
            ['Humor',            'হাস্যরস',                              'humor',                        'humour'],
            ['Center',           'কেন্দ্র',                               'center',                       'centre'],
            ['Theater',          'নাট্যশালা',                             'theater',                      'theatre'],
            ['Catalog',          'তালিকা',                                'catalog',                      'catalogue'],
            ['Program',          'কার্যক্রম',                             'program',                      'programme'],
            ['Analyze',          'বিশ্লেষণ করা',                         'analyze',                      'analyse'],
            ['Defense',          'প্রতিরক্ষা',                            'defense',                      'defence'],
            ['License',          'অনুমতিপত্র',                           'license',                      'licence'],
            ['Labor',            'পরিশ্রম',                               'labor',                        'labour'],
            ['Traveling',        'ভ্রমণ',                                 'traveling',                    'travelling'],
            ['Canceled',         'বাতিল',                                 'canceled',                     'cancelled'],
            ['Fulfillment',      'পরিপূর্ণতা',                           'fulfillment',                  'fulfilment'],

            // ── Verb Forms ──────────────────────────────────────────
            ['Learned',          'শিখেছি',                                'learned',                      'learnt'],
            ['Burned',           'পুড়েছে',                               'burned',                       'burnt'],
            ['Dreamed',          'স্বপ্ন দেখেছি',                        'dreamed',                      'dreamt'],
            ['Spelled',          'বানান করেছি',                          'spelled',                      'spelt'],
            ['Gotten',           'পেয়েছি',                               'gotten',                       'got'],
            ['Dove',             'ডুব দিয়েছে',                           'dove (past of dive)',          'dived'],

            // ── Miscellaneous ────────────────────────────────────────
            ['Soccer',           'ফুটবল খেলা',                           'soccer',                       'football'],
            ['Soccer field',     'খেলার মাঠ',                            'soccer field',                 'football pitch'],
            ['Fall',             'ঋতু',                                   'fall / autumn',                'autumn'],
            ['Mom',              'মা',                                     'mom / mama',                   'mum / mummy'],
            ['Zee',              'বর্ণমালার শেষ অক্ষর',                 'zee (Z)',                       'zed (Z)'],
            ['Vacation',         'ছুটি',                                  'vacation',                     'holiday'],
            ['Buddy',            'বন্ধু',                                 'buddy / pal',                  'mate / pal'],
            ['Cop',              'পুলিশ',                                 'cop / police officer',         'police officer / constable'],
            ['Jail',             'কারাগার',                               'jail',                         'prison / gaol'],
            ['Wrench',           'যন্ত্রপাতি',                            'wrench',                       'spanner'],
        ];

        $rows = [];
        foreach ($words as [$word, $meaning, $synonyms, $antonyms]) {
            $rows[] = [
                'word'       => $word,
                'meaning'    => $meaning,
                'synonyms'   => $synonyms,
                'antonyms'   => $antonyms,
                'lesson_id'  => $lessonId,
                'status'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('words')->insert($rows);
    }

    public function down(): void
    {
        $lesson = DB::table('lessons')
            ->where('type', 'american_british')
            ->first();

        if ($lesson) {
            DB::table('words')->where('lesson_id', $lesson->id)->delete();
            DB::table('lessons')->where('id', $lesson->id)->delete();
            DB::table('chapters')->where('id', $lesson->chapter_id)->delete();
        }
    }
};
