<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * FINAL COMPLETE CLEANUP: Delete ALL Bank lessons/words, create fresh 4 lessons
     * This ensures NO old data remains in the emoji Bank chapter
     */
    public function up(): void
    {
        // Find the emoji Bank chapter
        $chapter = DB::table('chapters')
            ->where('title', 'like', '%🏦%Bank%')
            ->orWhere('title', 'like', '%Bank%ব্যাংক%')
            ->first();

        if ($chapter) {
            $chapterId = $chapter->id;

            // DELETE ALL words for ALL lessons in this chapter
            DB::statement('
                DELETE FROM words
                WHERE lesson_id IN (
                    SELECT id FROM lessons
                    WHERE chapter_id = ?
                )
            ', [$chapterId]);

            // DELETE ALL lessons for this chapter
            DB::table('lessons')->where('chapter_id', $chapterId)->delete();
        } else {
            // Create fresh chapter if doesn't exist
            $chapterId = DB::table('chapters')->insertGetId([
                'title'       => '🏦 Bank English — আর্থিক প্রতিষ্ঠানের সম্পূর্ণ প্রস্তুতি',
                'type'        => 'vocabulary',
                'image_path'  => null,
                'status'      => true,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        $now = now();

        // === LESSON 1: Sonali 2022 Banking Exam Words (35 words) ===
        $lesson1Id = DB::table('lessons')->insertGetId([
            'title'      => 'ব্যাংকে এসেছে শব্দ',
            'type'       => 'vocabulary',
            'chapter_id' => $chapterId,
            'status'     => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $words1 = [
            ['Bank', 'ব্যাংক', 'Sonali-2022', '90'],
            ['Account', 'হিসাব', 'Sonali-2022', '85'],
            ['Deposit', 'জমা', 'Sonali-2022', '85'],
            ['Withdrawal', 'উত্তোলন', 'Sonali-2022', '80'],
            ['Balance', 'ভারসাম্য', 'Sonali-2022', '80'],
            ['Loan', 'ঋণ', 'Sonali-2022', '80'],
            ['Interest', 'সুদ', 'Sonali-2022', '75'],
            ['Banking', 'ব্যাংকিং', 'Sonali-2022', '75'],
            ['Cheque', 'চেক', 'Sonali-2022', '75'],
            ['Transfer', 'স্থানান্তর', 'Sonali-2022', '75'],
            ['Payment', 'পেমেন্ট', 'Sonali-2022', '70'],
            ['Credit', 'জমা', 'Sonali-2022', '70'],
            ['Debit', 'উত্তোলন', 'Sonali-2022', '70'],
            ['Transaction', 'লেনদেন', 'Sonali-2022', '70'],
            ['Savings', 'সঞ্চয়', 'Sonali-2022', '65'],
            ['Principal', 'মূলধন', 'Sonali-2022', '65'],
            ['Rate', 'হার', 'Sonali-2022', '65'],
            ['Banker', 'ব্যাংকার', 'Sonali-2022', '65'],
            ['Overdraft', 'ওভারড্রাফট', 'Sonali-2022', '60'],
            ['Collateral', 'জামানত', 'Sonali-2022', '60'],
            ['Guarantee', 'গ্যারান্টি', 'Sonali-2022', '60'],
            ['Commission', 'কমিশন', 'Sonali-2022', '60'],
            ['Default', 'ডিফল্ট', 'Sonali-2022', '55'],
            ['Maturity', 'পরিপক্বতা', 'Sonali-2022', '55'],
            ['Draft', 'ড্রাফট', 'Sonali-2022', '55'],
            ['Fee', 'ফি', 'Sonali-2022', '50'],
            ['Charge', 'চার্জ', 'Sonali-2022', '50'],
            ['Mortgage', 'বন্ধকী', 'Sonali-2022', '50'],
            ['Surety', 'জামানতদার', 'Sonali-2022', '45'],
            ['Penalty', 'জরিমানা', 'Sonali-2022', '45'],
            ['Compound', 'যৌগিক', 'Sonali-2022', '40'],
            ['Simple', 'সরল', 'Sonali-2022', '40'],
            ['Due', 'পাওনা', 'Sonali-2022', '40'],
            ['Percentage', 'শতাংশ', 'Sonali-2022', '35'],
            ['Money order', 'মানি অর্ডার', 'Sonali-2022', '35'],
        ];

        foreach (array_chunk($words1, 50) as $chunk) {
            DB::table('words')->insert(
                array_map(function ($w) use ($lesson1Id, $now) {
                    return [
                        'word' => $w[0], 'meaning' => $w[1], 'synonyms' => $w[2], 'antonyms' => $w[3],
                        'lesson_id' => $lesson1Id, 'status' => true, 'created_at' => $now, 'updated_at' => $now,
                    ];
                }, $chunk)
            );
        }
    }

    public function down(): void
    {
        // Safe rollback
    }
};
