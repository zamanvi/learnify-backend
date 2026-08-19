<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * FINAL COMPLETE BANK ENGLISH: All 4 lessons with complete, clean data
     * Lesson 1: Sonali 2022 Banking Exam Words (35 words, 90-35%)
     * Lesson 2: Probable Future Banking Words (30 words, 70-40%)
     * Lesson 3: Financial Theory Terms (25 words, 85-60%)
     * Lesson 4: Digital Banking & Fintech (20 words, 75-45%)
     */
    public function up(): void
    {
        // Find Bank chapter or create fresh
        $chapter = DB::table('chapters')
            ->where('title', 'like', '%Bank%')
            ->orWhere('title', 'like', '%ব্যাংক%')
            ->first();

        if ($chapter) {
            $chapterId = $chapter->id;
            // Delete ALL old words and lessons
            DB::statement('DELETE FROM words WHERE lesson_id IN (SELECT id FROM lessons WHERE chapter_id = ?)', [$chapterId]);
            DB::table('lessons')->where('chapter_id', $chapterId)->delete();
        } else {
            $chapterId = DB::table('chapters')->insertGetId([
                'title' => '🏦 Bank English — আর্থিক প্রতিষ্ঠানের সম্পূর্ণ প্রস্তুতি',
                'type' => 'vocabulary',
                'image_path' => null,
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $now = now();

        // ===== LESSON 1: Sonali 2022 Banking Exam Words (35 words) =====
        $lesson1Id = DB::table('lessons')->insertGetId([
            'title' => 'ব্যাংকে এসেছে শব্দ',
            'type' => 'vocabulary',
            'chapter_id' => $chapterId,
            'status' => true,
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

        $this->insertWords($words1, $lesson1Id, $now);

        // ===== LESSON 2: Probable Future Banking Words (30 words) =====
        $lesson2Id = DB::table('lessons')->insertGetId([
            'title' => 'আসতে পারে — সম্ভাব্য ব্যাংক শব্দ',
            'type' => 'vocabulary',
            'chapter_id' => $chapterId,
            'status' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $words2 = [
            ['Liquidity', 'তরলতা', 'Probable', '70'],
            ['Solvency', 'সামর্থ্য', 'Probable', '70'],
            ['Bankruptcy', 'দেউলিয়াত্ব', 'Probable', '70'],
            ['NPA', 'নন-পারফর্মিং', 'Probable', '65'],
            ['Credit score', 'ক্রেডিট স্কোর', 'Probable', '65'],
            ['KYC', 'কাস্টমার শনাক্ত', 'Probable', '65'],
            ['Fraud', 'জালিয়াতি', 'Probable', '60'],
            ['Insurance', 'বীমা', 'Probable', '60'],
            ['Claim', 'দাবি', 'Probable', '60'],
            ['Coverage', 'কভারেজ', 'Probable', '60'],
            ['Premium', 'প্রিমিয়াম', 'Probable', '55'],
            ['Risk', 'ঝুঁকি', 'Probable', '55'],
            ['Safe', 'সুরক্ষিত', 'Probable', '55'],
            ['Security', 'নিরাপত্তা', 'Probable', '55'],
            ['Vault', 'তিজোরি', 'Probable', '50'],
            ['Locker', 'লকার', 'Probable', '50'],
            ['Custody', 'হেফাজত', 'Probable', '50'],
            ['Trustee', 'ট্রাস্টি', 'Probable', '45'],
            ['Trust', 'ট্রাস্ট', 'Probable', '45'],
            ['Dividend', 'লভ্যাংশ', 'Probable', '45'],
            ['Portfolio', 'পোর্টফোলিও', 'Probable', '40'],
            ['Investment', 'বিনিয়োগ', 'Probable', '40'],
            ['Assets', 'সম্পদ', 'Probable', '40'],
            ['Liabilities', 'দায়বদ্ধতা', 'Probable', '40'],
            ['Capital', 'পুঁজি', 'Probable', '40'],
            ['Equity', 'সামগ্রিক মূল্য', 'Probable', '40'],
            ['Cash flow', 'নগদ প্রবাহ', 'Probable', '40'],
            ['Revenue', 'রাজস্ব', 'Probable', '40'],
            ['Profit', 'লাভ', 'Probable', '40'],
            ['Loss', 'ক্ষতি', 'Probable', '40'],
        ];

        $this->insertWords($words2, $lesson2Id, $now);

        // ===== LESSON 3: Financial Theory Terms (25 words) =====
        $lesson3Id = DB::table('lessons')->insertGetId([
            'title' => 'আর্থিক বিষয় — Banking Excellence',
            'type' => 'vocabulary',
            'chapter_id' => $chapterId,
            'status' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $words3 = [
            ['Economics', 'অর্থনীতি', 'Finance', '85'],
            ['Economy', 'অর্থব্যবস্থা', 'Finance', '85'],
            ['Finance', 'আর্থিক', 'Finance', '85'],
            ['Financial', 'আর্থিক', 'Finance', '80'],
            ['Market', 'বাজার', 'Finance', '80'],
            ['Price', 'মূল্য', 'Finance', '80'],
            ['Value', 'মূল্যবান', 'Finance', '80'],
            ['Cost', 'খরচ', 'Finance', '80'],
            ['Supply', 'সরবরাহ', 'Finance', '75'],
            ['Demand', 'চাহিদা', 'Finance', '75'],
            ['Trade', 'বাণিজ্য', 'Finance', '75'],
            ['Commerce', 'বাণিজ্য', 'Finance', '75'],
            ['Business', 'ব্যবসা', 'Finance', '75'],
            ['Industry', 'শিল্প', 'Finance', '70'],
            ['Production', 'উৎপাদন', 'Finance', '70'],
            ['Consumer', 'ভোক্তা', 'Finance', '70'],
            ['Customer', 'গ্রাহক', 'Finance', '70'],
            ['Vendor', 'বিক্রেতা', 'Finance', '65'],
            ['Supplier', 'সরবরাহকারী', 'Finance', '65'],
            ['Profit margin', 'লাভের হার', 'Finance', '60'],
            ['Tax', 'ট্যাক্স', 'Finance', '60'],
            ['Debt', 'ঋণ', 'Finance', '60'],
            ['Expense', 'ব্যয়', 'Finance', '60'],
            ['Income', 'আয়', 'Finance', '60'],
            ['Wage', 'মজুরি', 'Finance', '60'],
        ];

        $this->insertWords($words3, $lesson3Id, $now);

        // ===== LESSON 4: Digital Banking & Fintech (20 words) =====
        $lesson4Id = DB::table('lessons')->insertGetId([
            'title' => 'ডিজিটাল ব্যাংকিং — Modern Finance',
            'type' => 'vocabulary',
            'chapter_id' => $chapterId,
            'status' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $words4 = [
            ['Digital', 'ডিজিটাল', 'Fintech', '75'],
            ['Online', 'অনলাইন', 'Fintech', '75'],
            ['Mobile', 'মোবাইল', 'Fintech', '75'],
            ['App', 'অ্যাপ্লিকেশন', 'Fintech', '75'],
            ['Technology', 'প্রযুক্তি', 'Fintech', '70'],
            ['Payment', 'পেমেন্ট', 'Fintech', '70'],
            ['Transaction', 'লেনদেন', 'Fintech', '70'],
            ['Wallet', 'ডিজিটাল মানিব্যাগ', 'Fintech', '70'],
            ['Cryptocurrency', 'ক্রিপ্টোকারেন্সি', 'Fintech', '65'],
            ['Bitcoin', 'বিটকয়েন', 'Fintech', '65'],
            ['Blockchain', 'ব্লকচেইন', 'Fintech', '65'],
            ['Token', 'টোকেন', 'Fintech', '60'],
            ['Smart contract', 'স্মার্ট চুক্তি', 'Fintech', '60'],
            ['API', 'এপিআই', 'Fintech', '55'],
            ['Cloud', 'ক্লাউড', 'Fintech', '55'],
            ['Data', 'ডেটা', 'Fintech', '55'],
            ['Security', 'নিরাপত্তা', 'Fintech', '55'],
            ['Encryption', 'এনক্রিপশন', 'Fintech', '50'],
            ['Verification', 'যাচাইকরণ', 'Fintech', '50'],
            ['Authentication', 'সত্যতা প্রমাণ', 'Fintech', '50'],
        ];

        $this->insertWords($words4, $lesson4Id, $now);
    }

    private function insertWords($words, $lessonId, $now): void
    {
        foreach (array_chunk($words, 50) as $chunk) {
            DB::table('words')->insert(
                array_map(function ($w) use ($lessonId, $now) {
                    return [
                        'word' => $w[0],
                        'meaning' => $w[1],
                        'synonyms' => $w[2],
                        'antonyms' => $w[3],
                        'lesson_id' => $lessonId,
                        'status' => true,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }, $chunk)
            );
        }
    }

    public function down(): void {}
};
