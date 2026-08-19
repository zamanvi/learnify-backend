<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Adds JSC English First Paper vocabulary (8 units, 144 words) for Class VII-VIII
     * based on NCTB "English For Today" textbook themes. Simple 2-field structure
     * (word, meaning) matching the HSC/SSC pattern — no synonyms/antonyms/source
     * tracking needed for this age group's foundational vocabulary.
     *
     * Units:
     * 1. Friendship & People
     * 2. School & Education
     * 3. Family & Home
     * 4. Daily Life & Activities
     * 5. Nature & Environment
     * 6. Sports & Games
     * 7. Food & Cooking
     * 8. Health & Body
     *
     * Words are deduplicated globally across units and do not overlap with
     * SSC/HSC vocabulary (chapter_id 3/4) — this set targets foundational,
     * everyday words appropriate for Class 7-8 students.
     */
    public function up(): void
    {
        // Check if chapter already exists, otherwise create it
        $chapter = DB::table('chapters')
            ->where('title', 'JSC english first paper words meaning')
            ->first();

        if (!$chapter) {
            $chapterId = DB::table('chapters')->insertGetId([
                'title'       => 'JSC english first paper words meaning',
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
            'Friendship & People' => [
                ['Friend', 'বন্ধু'],
                ['Honest', 'সৎ'],
                ['Kind', 'দয়ালু'],
                ['Faithful', 'বিশ্বস্ত'],
                ['Loyal', 'আনুগত্যশীল'],
                ['Trust', 'বিশ্বাস'],
                ['Friendship', 'বন্ধুত্ব'],
                ['Promise', 'প্রতিশ্রুতি'],
                ['Sincere', 'আন্তরিক'],
                ['Generous', 'উদার'],
                ['Selfish', 'স্বার্থপর'],
                ['Betray', 'বিশ্বাসঘাতকতা করা'],
                ['Quarrel', 'ঝগড়া করা'],
                ['Forgive', 'ক্ষমা করা'],
                ['Reconcile', 'মিলিত হওয়া'],
                ['Support', 'সমর্থন করা'],
                ['Companion', 'সঙ্গী'],
                ['Comrade', 'সহযোগী'],
            ],
            'School & Education' => [
                ['School', 'স্কুল'],
                ['Student', 'ছাত্র'],
                ['Teacher', 'শিক্ষক'],
                ['Study', 'পড়াশোনা করা'],
                ['Lesson', 'পাঠ'],
                ['Classroom', 'শ্রেণীকক্ষ'],
                ['Homework', 'বাড়ির কাজ'],
                ['Exam', 'পরীক্ষা'],
                ['Subject', 'বিষয়'],
                ['Grade', 'শ্রেণী'],
                ['Mark', 'নম্বর'],
                ['Intelligent', 'বুদ্ধিমান'],
                ['Hardworking', 'পরিশ্রমী'],
                ['Diligent', 'নিষ্ঠাবান'],
                ['Lazy', 'অলস'],
                ['Educate', 'শিক্ষা দেওয়া'],
                ['Knowledge', 'জ্ঞান'],
                ['Ignorant', 'অজ্ঞ'],
            ],
            'Family & Home' => [
                ['Family', 'পরিবার'],
                ['Mother', 'মা'],
                ['Father', 'বাবা'],
                ['Brother', 'ভাই'],
                ['Sister', 'বোন'],
                ['Grandfather', 'দাদা'],
                ['Grandmother', 'দাদী'],
                ['Uncle', 'চাচা'],
                ['Aunt', 'চাচী'],
                ['Cousin', 'চাচাত ভাই'],
                ['Home', 'বাড়ি'],
                ['House', 'ঘর'],
                ['Room', 'কক্ষ'],
                ['Kitchen', 'রসোই'],
                ['Bedroom', 'শোবার ঘর'],
                ['Living room', 'বসার ঘর'],
                ['Love', 'ভালোবাসা'],
                ['Care', 'যত্ন'],
            ],
            'Daily Life & Activities' => [
                ['Morning', 'সকাল'],
                ['Afternoon', 'দুপুর'],
                ['Evening', 'সন্ধ্যা'],
                ['Night', 'রাত'],
                ['Wake up', 'ঘুম থেকে জাগা'],
                ['Sleep', 'ঘুম'],
                ['Breakfast', 'নাস্তা'],
                ['Lunch', 'দুপুরের খাবার'],
                ['Dinner', 'রাতের খাবার'],
                ['Eat', 'খাওয়া'],
                ['Drink', 'পানীয় পান করা'],
                ['Work', 'কাজ করা'],
                ['Play', 'খেলা করা'],
                ['Exercise', 'ব্যায়াম'],
                ['Rest', 'বিশ্রাম'],
                ['Relax', 'শিথিল হওয়া'],
                ['Busy', 'ব্যস্ত'],
                ['Free', 'অবসর'],
            ],
            'Nature & Environment' => [
                ['Nature', 'প্রকৃতি'],
                ['Tree', 'গাছ'],
                ['Flower', 'ফুল'],
                ['Bird', 'পাখি'],
                ['Animal', 'পশু'],
                ['Forest', 'বন'],
                ['River', 'নদী'],
                ['Mountain', 'পর্বত'],
                ['Sky', 'আকাশ'],
                ['Cloud', 'মেঘ'],
                ['Rain', 'বৃষ্টি'],
                ['Sunshine', 'রোদ'],
                ['Wind', 'বাতাস'],
                ['Beautiful', 'সুন্দর'],
                ['Green', 'সবুজ'],
                ['Fresh', 'তাজা'],
                ['Pollute', 'দূষণ করা'],
                ['Protect', 'রক্ষা করা'],
            ],
            'Sports & Games' => [
                ['Sport', 'খেলাধুলা'],
                ['Game', 'খেলা'],
                ['Football', 'ফুটবল'],
                ['Cricket', 'ক্রিকেট'],
                ['Tennis', 'টেনিস'],
                ['Basketball', 'বাস্কেটবল'],
                ['Player', 'খেলোয়াড়'],
                ['Team', 'দল'],
                ['Win', 'জেতা'],
                ['Lose', 'হারানো'],
                ['Draw', 'ড্র'],
                ['Score', 'গোল করা'],
                ['Goal', 'গোল'],
                ['Match', 'খেলা'],
                ['Coach', 'প্রশিক্ষক'],
                ['Practice', 'অনুশীলন'],
                ['Skill', 'দক্ষতা'],
                ['Strong', 'শক্তিশালী'],
            ],
            'Food & Cooking' => [
                ['Food', 'খাবার'],
                ['Cook', 'রান্না করা'],
                ['Dish', 'খাবার'],
                ['Rice', 'ভাত'],
                ['Bread', 'রুটি'],
                ['Vegetable', 'সবজি'],
                ['Fruit', 'ফল'],
                ['Meat', 'মাংস'],
                ['Fish', 'মাছ'],
                ['Egg', 'ডিম'],
                ['Milk', 'দুধ'],
                ['Cheese', 'পনির'],
                ['Salt', 'লবণ'],
                ['Sugar', 'চিনি'],
                ['Oil', 'তেল'],
                ['Spice', 'মশলা'],
                ['Delicious', 'সুস্বাদু'],
            ],
            'Health & Body' => [
                ['Health', 'স্বাস্থ্য'],
                ['Healthy', 'স্বাস্থ্যকর'],
                ['Sick', 'অসুস্থ'],
                ['Doctor', 'ডাক্তার'],
                ['Medicine', 'ওষুধ'],
                ['Hospital', 'হাসপাতাল'],
                ['Head', 'মাথা'],
                ['Heart', 'হৃদয়'],
                ['Hand', 'হাত'],
                ['Foot', 'পা'],
                ['Eye', 'চোখ'],
                ['Ear', 'কান'],
                ['Nose', 'নাক'],
                ['Mouth', 'মুখ'],
                ['Tooth', 'দাঁত'],
                ['Pain', 'ব্যথা'],
                ['Fever', 'জ্বর'],
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
        $chapter = DB::table('chapters')
            ->where('title', 'JSC english first paper words meaning')
            ->first();

        if ($chapter) {
            DB::table('words')->whereIn('lesson_id', function ($query) use ($chapter) {
                $query->select('id')
                    ->from('lessons')
                    ->where('chapter_id', $chapter->id);
            })->delete();

            DB::table('lessons')->where('chapter_id', $chapter->id)->delete();
        }
    }
};
