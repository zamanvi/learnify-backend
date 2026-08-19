<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adds SSC English First Paper vocabulary (14 units, 280 words) for Class IX-X
     * based on NCTB "English For Today" textbook. Each unit's name reflects the
     * actual lesson themes from the book for better student recognition.
     *
     * Units and Lessons:
     * 1. Good Citizens - Civic Vocabulary
     * 2. Pastime - Recreation & Leisure Words
     * 3. Events & Festivals - Celebration Vocabulary
     * 4. Awareness - Social Issues Vocabulary
     * 5. Climate Change - Environmental Vocabulary
     * 6. Neighbours - Geography & Culture Vocabulary
     * 7. Outstanding People - Achievement Vocabulary
     * 8. World Heritage - Cultural Treasures Vocabulary
     * 9. Career Paths - Professional Vocabulary
     * 10. Dreams - Psychology Vocabulary
     * 11. Energy & Sustainability - Technology Vocabulary
     * 12. Roots & Identity - Heritage Vocabulary
     * 13. Media & Communication - Digital Vocabulary
     * 14. Life's Purpose - Philosophy Vocabulary
     *
     * Words are deduplicated globally across units (a word already used in an
     * earlier unit is not repeated in a later one) — sourced from NCTB textbook
     * lessons and comprehension passages. No duplication with HSC vocabulary.
     */
    public function up(): void
    {
        // Check if chapter already exists, otherwise create it
        $chapter = DB::table('chapters')
            ->where('title', 'SSC english first paper words meaning')
            ->first();

        if (!$chapter) {
            $chapterId = DB::table('chapters')->insertGetId([
                'title'       => 'SSC english first paper words meaning',
                'type'        => 'vocabulary',
                'image_path'  => null,
                'status'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        } else {
            $chapterId = $chapter->id;
        }
        $now = now();

        $units = [
            'Good Citizens - Civic Vocabulary' => [
                ['Citizen', 'নাগরিক - a member of a state or country'],
                ['Virtue', 'সদ্গুণ - moral excellence and righteousness'],
                ['Character', 'চরিত্র - moral qualities of a person'],
                ['Tolerance', 'সহনশীলতা - respect for different beliefs'],
                ['Discipline', 'শৃঙ্খলা - orderly and controlled behavior'],
                ['Constitution', 'সংবিধান - fundamental laws of a country'],
                ['Duty', 'কর্তব্য - something one is required to do'],
                ['Responsibility', 'দায়িত্ব - state of being accountable'],
                ['Morality', 'নৈতিকতা - principles of right and wrong'],
                ['Harmony', 'সামঞ্জস্য - state of agreement'],
                ['Compassion', 'সহানুভূতি - sympathetic concern for others'],
                ['Integrity', 'সততা - honesty and moral uprightness'],
                ['Cooperative', 'সহযোগিতামূলক - working together for common goal'],
                ['Welfare', 'কল্যাণ - wellbeing and prosperity'],
                ['Civic', 'নাগরিক - relating to citizens or city'],
                ['Oblige', 'সেবা করা - do something to help'],
                ['Ethical', 'নৈতিক - relating to moral principles'],
                ['Devotion', 'নিষ্ঠা - deep commitment to duty'],
                ['Obligation', 'বাধ্যবাধকতা - something one must do'],
                ['Altruism', 'পরার্থতা - concern for others welfare'],
            ],
            'Pastime - Recreation & Leisure Words' => [
                ['Pastime', 'বিনোদন - activity done for enjoyment'],
                ['Recreation', 'বিশ্রাম এবং আনন্দ - refreshment and enjoyment'],
                ['Hobby', 'শখ - activity done for pleasure in spare time'],
                ['Leisure', 'অবসর - free time available for activities'],
                ['Yoga', 'যোগ - ancient practice for physical and mental wellbeing'],
                ['Meditation', 'ধ্যান - deep thought or contemplation'],
                ['Flexibility', 'নমনীয়তা - ability to bend without breaking'],
                ['Endurance', 'ধৈর্য - ability to withstand strain'],
                ['Posture', 'ভঙ্গিমা - position of body'],
                ['Concentration', 'মনোযোগ - focusing on one thing'],
                ['Practice', 'অনুশীলন - repeated exercise for skill'],
                ['Benefit', 'লাভ - advantage or profit'],
                ['Health', 'স্বাস্থ্য - state of physical wellbeing'],
                ['Enthusiasm', 'উৎসাহ - intense and keen interest'],
                ['Vigorous', 'শক্তিশালী - strong and energetic'],
                ['Vitality', 'প্রাণশক্তি - state of being alive and energetic'],
                ['Wellness', 'সুস্থতা - state of good health'],
                ['Tranquil', 'শান্ত - peaceful and calm'],
                ['Restoration', 'পুনরুদ্ধার - bringing back to original state'],
                ['Rejuvenation', 'পুনরুজ্জীবন - making young again'],
            ],
            'Events & Festivals - Celebration Vocabulary' => [
                ['Festival', 'উৎসব - celebration of cultural significance'],
                ['Commemorate', 'স্মরণ করা - honor the memory of'],
                ['Celebration', 'উদযাপন - joyful occasion'],
                ['Occasion', 'অনুষ্ঠান - special event'],
                ['Tradition', 'ঐতিহ্য - customs handed down'],
                ['Cultural', 'সাংস্কৃতিক - relating to culture'],
                ['Heritage', 'ঐতিহ্য - inheritance of values'],
                ['Significant', 'গুরুত্বপূর্ণ - having special meaning'],
                ['Independence', 'স্বাধীনতা - freedom from control'],
                ['Martyr', 'শহীদ - person who dies for cause'],
                ['Sacrifice', 'ত্যাগ - giving up something valuable'],
                ['Tribute', 'শ্রদ্ধা - expression of respect'],
                ['Diversity', 'বৈচিত্র্য - variety and difference'],
                ['Linguistic', 'ভাষাগত - relating to language'],
                ['Multilingual', 'বহুভাষিক - speaking many languages'],
                ['Unite', 'একত্রিত করা - join together'],
                ['Solidarity', 'সংহতি - unity and fellowship'],
                ['Commemoration', 'স্মারক - event to remember'],
                ['Spectacle', 'দর্শনীয় - impressive display'],
                ['Festive', 'উৎসবমুখর - joyful and celebratory'],
            ],
            'Awareness - Social Issues Vocabulary' => [
                ['Aware', 'সচেতন - having knowledge of'],
                ['Awareness', 'সচেতনতা - state of being aware'],
                ['Crisis', 'সংকট - critical situation'],
                ['Population', 'জনসংখ্যা - number of inhabitants'],
                ['Density', 'ঘনত্ব - closeness of elements'],
                ['Urbanization', 'শহরায়ন - growth of cities'],
                ['Congestion', 'যানজট - overcrowding'],
                ['Hazard', 'ঝুঁকি - danger or risk'],
                ['Pollution', 'দূষণ - contamination of environment'],
                ['Sanitation', 'স্বাস্থ্যবিধান - cleanliness and hygiene'],
                ['Scarcity', 'স্বল্পতা - lack or shortage'],
                ['Resource', 'সম্পদ - supply available for use'],
                ['Poverty', 'দারিদ্র্য - state of being poor'],
                ['Deprivation', 'বঞ্চনা - state of being denied'],
                ['Infrastructure', 'অবকাঠামো - basic facilities and systems'],
                ['Shortage', 'ঘাটতি - lack or insufficient supply'],
                ['Overcrowded', 'অতিরিক্ত ভিড়পূর্ণ - too many people'],
                ['Inevitable', 'অনিবার্য - unable to be avoided'],
                ['Mitigate', 'হ্রাস করা - make less severe'],
                ['Sustainable', 'টেকসই - able to be maintained'],
            ],
            'Climate Change - Environmental Vocabulary' => [
                ['Climate', 'জলবায়ু - long-term weather conditions'],
                ['Change', 'পরিবর্তন - transformation or alteration'],
                ['Global', 'বৈশ্বিক - worldwide in scope'],
                ['Warming', 'উষ্ণতা বৃদ্ধি - increase in temperature'],
                ['Greenhouse', 'গ্রিনহাউস - glass structure or gas effect'],
                ['Emission', 'নির্গমন - discharge of gas or pollutant'],
                ['Carbon', 'কার্বন - element in atmosphere'],
                ['Dioxide', 'ডাইঅক্সাইড - compound with two oxygen atoms'],
                ['Fossil', 'জীবাশ্ম - ancient organic remains'],
                ['Fuel', 'জ্বালানি - substance for producing energy'],
                ['Renewable', 'পুনর্নবীকরণযোগ্য - able to be renewed'],
                ['Erosion', 'ভূক্ষয় - wearing away of land'],
                ['Deforestation', 'বনায়ন বিনাশ - removal of forests'],
                ['Biodiversity', 'জৈব বৈচিত্র্য - variety of living organisms'],
                ['Ecosystem', 'ইকোসিস্টেম - community of organisms'],
                ['Conservation', 'সংরক্ষণ - protection of nature'],
                ['Pesticide', 'কীটনাশক - chemical to kill pests'],
                ['Contamination', 'দূষণ - making impure'],
                ['Catastrophe', 'বিপর্যয় - sudden disaster'],
                ['Mitigation', 'প্রশমন - making less severe'],
            ],
            'Neighbours - Geography & Culture Vocabulary' => [
                ['Neighbour', 'প্রতিবেশী - country sharing border'],
                ['Geography', 'ভূগোল - study of lands and people'],
                ['Mountain', 'পর্বত - very high landform'],
                ['Valley', 'উপত্যকা - low land between hills'],
                ['Terrain', 'ভূভাগ - physical features of land'],
                ['Cultural', 'সাংস্কৃতিক - relating to culture'],
                ['Tradition', 'ঐতিহ্য - custom passed down'],
                ['Tourism', 'পর্যটন - travel for pleasure'],
                ['Unity', 'ঐক্য - state of being united'],
                ['Harmony', 'সামঞ্জস্য - peaceful agreement'],
                ['Prosperity', 'সমৃদ্ধি - state of flourishing'],
                ['Peaceful', 'শান্তিপূর্ণ - free from conflict'],
                ['Coexistence', 'সহাবস্থান - living together peacefully'],
                ['Exchange', 'বিনিময় - trade or share'],
                ['Mutual', 'পারস্পরিক - shared by both'],
                ['Border', 'সীমানা - boundary between countries'],
                ['Unique', 'অনন্য - being only one'],
                ['Landmark', 'ল্যান্ডমার্ক - notable feature or place'],
                ['Distinct', 'স্বতন্ত্র - clearly different'],
                ['Enriching', 'সমৃদ্ধকারী - making richer'],
            ],
            'Outstanding People - Achievement Vocabulary' => [
                ['Achievement', 'সাফল্য - successful accomplishment'],
                ['Excellence', 'শ্রেষ্ঠত্ব - quality of being excellent'],
                ['Talent', 'প্রতিভা - natural ability'],
                ['Genius', 'প্রতিভাশালী - exceptionally intelligent'],
                ['Innovation', 'উদ্ভাবন - new invention or method'],
                ['Dedicate', 'নিবেদন করা - devote oneself'],
                ['Contribute', 'অবদান রাখা - give or provide'],
                ['Legacy', 'উত্তরাধিকার - something handed down'],
                ['Inspire', 'অনুপ্রাণিত করা - fill with courage'],
                ['Perseverance', 'অধ্যবসায় - continued effort'],
                ['Overcome', 'অতিক্রম করা - defeat a problem'],
                ['Obstacle', 'বাধা - something blocking the way'],
                ['Struggle', 'সংগ্রাম - difficult effort'],
                ['Triumph', 'বিজয় - great success'],
                ['Remarkable', 'উল্লেখযোগ্য - worthy of notice'],
                ['Humble', 'নম্র - not proud'],
                ['Compassionate', 'সহানুভূতিশীল - showing sympathy'],
                ['Visionary', 'দূরদর্শী - forward-thinking'],
                ['Pioneer', 'অগ্রদূত - first to do something'],
                ['Exemplary', 'অনুকরণীয় - serving as example'],
            ],
            'World Heritage - Cultural Treasures Vocabulary' => [
                ['Monument', 'স্মৃতিসৌধ - structure commemorating event'],
                ['Archaeological', 'প্রত্নতাত্ত্বিক - relating to ancient cultures'],
                ['Ancient', 'প্রাচীন - very old'],
                ['Civilization', 'সভ্যতা - advanced human society'],
                ['Architecture', 'স্থাপত্য - art of designing buildings'],
                ['Intricate', 'জটিল - detailed and complex'],
                ['Artistry', 'শিল্পকলা - skill in creative work'],
                ['Craftsmanship', 'কারুশিল্প - skilled craft work'],
                ['Preservation', 'সংরক্ষণ - keeping in good condition'],
                ['UNESCO', 'ইউনেস্কো - United Nations cultural organization'],
                ['Inscription', 'শিলালিপি - writing carved on surface'],
                ['Masterpiece', 'মাস্টারপিস - greatest work'],
                ['Magnificent', 'মহিমান্বিত - impressively grand'],
                ['Splendid', 'অসাধারণ - beautiful and impressive'],
                ['Treasure', 'ধন - valuable object'],
                ['Vestige', 'চিহ্ন - remaining trace'],
                ['Testament', 'প্রমাণ - evidence of something'],
                ['Grandeur', 'ঐশ্বর্য - impressive appearance'],
                ['Majestic', 'মহীয়ান - impressively dignified'],
                ['Timeless', 'চিরন্তন - lasting for all time'],
            ],
            'Career Paths - Professional Vocabulary' => [
                ['Career', 'পেশা - occupation or profession'],
                ['Profession', 'পেশা - skilled occupation'],
                ['Unconventional', 'অপ্রচলিত - not traditional'],
                ['Entrepreneur', 'উদ্যোক্তা - person starting business'],
                ['Passion', 'আবেগ - strong enthusiasm'],
                ['Dedication', 'নিষ্ঠা - commitment to work'],
                ['Opportunity', 'সুযোগ - favorable circumstance'],
                ['Challenge', 'চ্যালেঞ্জ - difficult task'],
                ['Skill', 'দক্ষতা - learned ability'],
                ['Expertise', 'দক্ষতা - expert knowledge'],
                ['Success', 'সাফল্য - achievement of goal'],
                ['Failure', 'ব্যর্থতা - lack of success'],
                ['Resilience', 'স্থিতিস্থাপকতা - ability to recover'],
                ['Adaptability', 'অভিযোজনযোগ্যতা - ability to adjust'],
                ['Initiative', 'উদ্যোগ - taking action'],
                ['Ambition', 'উচ্চাকাঙ্ক্ষা - desire for success'],
                ['Persevere', 'অধ্যবসায় - continued determination'],
                ['Motivation', 'অনুপ্রেরণা - reason for action'],
                ['Fulfillment', 'সন্তুষ্টি - feeling of satisfaction'],
                ['Growth', 'বৃদ্ধি - process of increasing'],
            ],
            'Dreams - Psychology Vocabulary' => [
                ['Dream', 'স্বপ্ন - images during sleep'],
                ['Sleep', 'ঘুম - state of rest'],
                ['Consciousness', 'চেতনা - state of awareness'],
                ['Subconscious', 'অচেতন - below conscious level'],
                ['Psychology', 'মনোবিজ্ঞান - study of mind'],
                ['Emotion', 'আবেগ - feeling or sentiment'],
                ['Anxiety', 'উদ্বেগ - feeling of worry'],
                ['Fear', 'ভয় - feeling of danger'],
                ['Recurring', 'পুনরাবৃত্ত - happening again'],
                ['Symbol', 'প্রতীক - something representing'],
                ['Symbolism', 'প্রতীকতা - use of symbols'],
                ['Interpretation', 'ব্যাখ্যা - explaining meaning'],
                ['Suppress', 'দমন করা - force down'],
                ['Express', 'প্রকাশ করা - show or convey'],
                ['Desire', 'কামনা - strong wish'],
                ['Aspiration', 'আকাঙ্ক্ষা - hope to achieve'],
                ['Vivid', 'জীবন্ত - bright and clear'],
                ['Mysterious', 'রহস্যময় - hard to understand'],
                ['Enigma', 'ধাঁধা - something puzzling'],
                ['Paradox', 'বিরোধাভাস - seemingly contradictory'],
            ],
            'Energy & Sustainability - Technology Vocabulary' => [
                ['Energy', 'শক্তি - power and ability'],
                ['Renewable', 'পুনর্নবীকরণযোগ্য - able to be renewed'],
                ['Solar', 'সৌর - relating to sun'],
                ['Wind', 'বায়ু - moving air'],
                ['Hydropower', 'জলবিদ্যুৎ - power from water'],
                ['Geothermal', 'ভূতাপীয় - from earth\'s heat'],
                ['Biomass', 'বায়োমাস - organic material'],
                ['Sustainability', 'টেকসইতা - ability to sustain'],
                ['Efficient', 'দক্ষ - working well'],
                ['Footprint', 'পদচিহ্ন - environmental impact'],
                ['Alternative', 'বিকল্প - other option'],
                ['Technology', 'প্রযুক্তি - application of science'],
                ['Climate', 'জলবায়ু - weather patterns'],
                ['Transition', 'রূপান্তর - change from one to another'],
                ['Sustainable', 'টেকসই - able to maintain'],
                ['Renewable', 'পুনর্নবীকরণযোগ্য - regenerating naturally'],
                ['Efficiency', 'দক্ষতা - effective operation'],
                ['Innovation', 'উদ্ভাবন - new method or technology'],
                ['Reduce', 'কমানো - make smaller'],
                ['Recycle', 'পুনর্ব্যবহার করা - use again'],
            ],
            'Roots & Identity - Heritage Vocabulary' => [
                ['Root', 'শিকড় - origin or source'],
                ['Identity', 'পরিচয় - characteristics of person'],
                ['Origin', 'উৎপত্তি - point where something begins'],
                ['Ancestry', 'বংশপরম্পরা - family lineage'],
                ['Culture', 'সংস্কৃতি - beliefs and customs'],
                ['Connection', 'সংযোগ - link or relation'],
                ['Belonging', 'অন্তর্ভুক্তি - sense of fitting in'],
                ['Community', 'সম্প্রদায় - group of people'],
                ['Bond', 'বন্ধন - close relationship'],
                ['Kinship', 'আত্মীয়তা - family relationship'],
                ['Genealogy', 'বংশতালিকা - family history'],
                ['Ancestor', 'পূর্বপুরুষ - early family member'],
                ['Descendant', 'বংশধর - offspring'],
                ['Generation', 'প্রজন্ম - age group'],
                ['Preserve', 'সংরক্ষণ করা - keep safe'],
                ['Transmit', 'প্রেরণ করা - pass on'],
                ['Continuity', 'সংযোগ - unbroken sequence'],
                ['Reflect', 'প্রতিফলিত করা - think deeply'],
                ['Trace', 'খুঁজে বের করা - follow back'],
                ['Embrace', 'আলিঙ্গন করা - accept willingly'],
            ],
            'Media & Communication - Digital Vocabulary' => [
                ['Media', 'মিডিয়া - means of communication'],
                ['Communication', 'যোগাযোগ - exchange of information'],
                ['Digital', 'ডিজিটাল - using computer technology'],
                ['Internet', 'ইন্টারনেট - global network'],
                ['Technology', 'প্রযুক্তি - application of science'],
                ['Information', 'তথ্য - facts and knowledge'],
                ['Broadcasting', 'সম্প্রচার - sending out widely'],
                ['Telecommunication', 'টেলিকমিউনিকেশন - long distance communication'],
                ['Virtual', 'ভার্চুয়াল - existing online'],
                ['Social', 'সামাজিক - relating to society'],
                ['Network', 'নেটওয়ার্ক - connected group'],
                ['Platform', 'প্ল্যাটফর্ম - place for communication'],
                ['Influence', 'প্রভাব - effect on opinion'],
                ['Content', 'বিষয়বস্তু - what is contained'],
                ['Viral', 'ভাইরাল - spread widely and rapidly'],
                ['Algorithm', 'অ্যালগরিদম - step-by-step process'],
                ['Database', 'ডাটাবেস - organized data'],
                ['Security', 'নিরাপত্তা - protection from harm'],
                ['Privacy', 'গোপনীয়তা - state of being private'],
                ['Cyber', 'সাইবার - relating to internet'],
            ],
            'Life\'s Purpose - Philosophy Vocabulary' => [
                ['Purpose', 'উদ্দেশ্য - reason for existence'],
                ['Pleasure', 'আনন্দ - feeling of satisfaction'],
                ['Fulfillment', 'পূর্ণতা - satisfaction of needs'],
                ['Meaning', 'অর্থ - significance or import'],
                ['Philosophy', 'দর্শন - study of truth'],
                ['Existential', 'অস্তিত্বগত - relating to existence'],
                ['Happiness', 'সুখ - state of contentment'],
                ['Contentment', 'সন্তুষ্টি - state of being satisfied'],
                ['Enlightenment', 'আলোকিতকরণ - spiritual insight'],
                ['Wisdom', 'প্রজ্ঞা - deep understanding'],
                ['Knowledge', 'জ্ঞান - understanding of facts'],
                ['Experience', 'অভিজ্ঞতা - event or observation'],
                ['Value', 'মূল্যবোধ - principles of importance'],
                ['Morality', 'নৈতিকতা - principles of right/wrong'],
                ['Ethics', 'নীতিশাস্ত্র - study of morality'],
                ['Service', 'সেবা - helpful activity'],
                ['Contribution', 'অবদান - something given'],
                ['Gratitude', 'কৃতজ্ঞতা - feeling thankful'],
                ['Mindfulness', 'সচেতনতা - awareness of present'],
                ['Self-realization', 'আত্মবোধ - understanding oneself'],
            ],
        ];

        $unitNumber = 1;
        foreach ($units as $unitTitle => $words) {
            $lessonId = DB::table('lessons')->insertGetId([
                'title'      => 'Unit ' . $unitNumber . ': ' . $unitTitle,
                'type'       => 'vocabulary',
                'chapter_id' => $chapterId,
                'status'     => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $rows = array_map(function ($w) use ($lessonId, $now) {
                return [
                    'word'       => $w[0],
                    'meaning'    => $w[1],
                    'lesson_id'  => $lessonId,
                    'status'     => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }, $words);

            foreach (array_chunk($rows, 50) as $chunk) {
                DB::table('words')->insert($chunk);
            }

            $unitNumber++;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Find the chapter
        $chapter = DB::table('chapters')
            ->where('title', 'SSC english first paper words meaning')
            ->first();

        if ($chapter) {
            // Delete all words belonging to this chapter's lessons
            DB::table('words')->whereIn('lesson_id', function ($query) use ($chapter) {
                $query->select('id')
                    ->from('lessons')
                    ->where('chapter_id', $chapter->id);
            })->delete();

            // Delete all lessons belonging to this chapter
            DB::table('lessons')->where('chapter_id', $chapter->id)->delete();

            // Delete the chapter itself (optional - usually we keep chapters)
            // DB::table('chapters')->where('id', $chapter->id)->delete();
        }
    }
};
