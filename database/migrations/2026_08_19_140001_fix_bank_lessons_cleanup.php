<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Fix: Delete ALL old Bank lessons first, then recreate 4 new ones cleanly
     * Handles the case where old 2 lessons + new 4 lessons both exist
     */
    public function up(): void
    {
        // Find Bank chapter by exact title OR partial match
        $chapter = DB::table('chapters')
            ->where('title', 'Bank English — ব্যাংক ও আর্থিক প্রতিষ্ঠানের প্রস্তুতি')
            ->orWhere('title', '🏦 Bank English — আর্থিক প্রতিষ্ঠানের সম্পূর্ণ প্রস্তুতি')
            ->orWhere('title', 'like', '%Bank%')
            ->first();

        if ($chapter) {
            // DELETE ALL words for ALL lessons in this chapter
            DB::statement('
                DELETE FROM words
                WHERE lesson_id IN (
                    SELECT id FROM lessons
                    WHERE chapter_id = ' . $chapter->id . '
                )
            ');

            // DELETE ALL lessons for this chapter
            DB::table('lessons')->where('chapter_id', $chapter->id)->delete();

            // UPDATE chapter title to new consistent name
            DB::table('chapters')
                ->where('id', $chapter->id)
                ->update([
                    'title' => '🏦 Bank English — আর্থিক প্রতিষ্ঠানের সম্পূর্ণ প্রস্তুতি',
                    'updated_at' => now(),
                ]);

            $chapterId = $chapter->id;
        } else {
            // Create new chapter if doesn't exist
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

        // ========== Create 4 Fresh Lessons ==========

        // LESSON 1
        $lesson1Id = DB::table('lessons')->insertGetId([
            'title'      => 'ব্যাংকে এসেছে শব্দ',
            'type'       => 'vocabulary',
            'chapter_id' => $chapterId,
            'status'     => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Lesson 1: Sonali 2022 Banking Exam Words with Importance %
        // High % = frequently repeated in exams, Low % = occasionally appeared
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

        // LESSON 2
        $lesson2Id = DB::table('lessons')->insertGetId([
            'title'      => 'আসতে পারে — সম্ভাব্য ব্যাংক শব্দ',
            'type'       => 'vocabulary',
            'chapter_id' => $chapterId,
            'status'     => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $words2 = [
            ['Liquidity', 'তরলতা', 'Probable', 'cash availability'],
            ['Solvency', 'সামর্থ্য', 'Probable', 'ability to pay'],
            ['Insolvency', 'অসামর্থ্য', 'Probable', 'inability to pay'],
            ['Bankruptcy', 'দেউলিয়াত্ব', 'Probable', 'legal insolvency'],
            ['NPA', 'নন-পারফর্মিং', 'Probable', 'bad loan'],
            ['Credit score', 'ক্রেডিট স্কোর', 'Probable', 'creditworthiness'],
            ['KYC', 'কাস্টমার শনাক্তকরণ', 'Probable', 'identification'],
            ['Fraud', 'জালিয়াতি', 'Probable', 'deception'],
            ['Insurance', 'বীমা', 'Probable', 'protection'],
            ['Claim', 'দাবি', 'Probable', 'request payment'],
            ['Coverage', 'কভারেজ', 'Probable', 'protection range'],
            ['Premium', 'প্রিমিয়াম', 'Probable', 'insurance fee'],
            ['Risk', 'ঝুঁকি', 'Probable', 'danger'],
            ['Safe', 'সুরক্ষিত', 'Probable', 'protected'],
            ['Security', 'নিরাপত্তা', 'Probable', 'protection'],
            ['Vault', 'তিজোরি', 'Probable', 'money storage'],
            ['Locker', 'লকার', 'Probable', 'safe box'],
            ['Custody', 'কাস্টডি', 'Probable', 'keeping'],
            ['Trustee', 'ট্রাস্টি', 'Probable', 'one who trusts'],
            ['Trust', 'ট্রাস্ট', 'Probable', 'confidence'],
            ['Escrow', 'এসক্রো', 'Probable', 'held money'],
            ['Pledge', 'প্রতিশ্রুতি', 'Probable', 'promise'],
            ['Mortgage', 'বন্ধকী', 'Probable', 'property security'],
            ['Lien', 'স্থায়ী দাবি', 'Probable', 'legal claim'],
            ['Attachment', 'সংযোজন', 'Probable', 'seize property'],
            ['Liquidation', 'তরলীকরণ', 'Probable', 'asset conversion'],
            ['Restructuring', 'পুনর্গঠন', 'Probable', 'reorganization'],
            ['Refinancing', 'পুনর্অর্থায়ন', 'Probable', 'new loan'],
            ['Consolidation', 'সমন্বয়', 'Probable', 'combining'],
            ['Merger', 'সংযুক্তি', 'Probable', 'joining'],
            ['Acquisition', 'অধিগ্রহণ', 'Probable', 'taking over'],
            ['Takeover', 'দখল', 'Probable', 'gaining control'],
            ['Leverage', 'লিভারেজ', 'Probable', 'debt for investment'],
            ['Derivative', 'ডেরিভেটিভ', 'Probable', 'complex investment'],
            ['Futures', 'ফিউচার্স', 'Probable', 'future contract'],
            ['Options', 'অপশন', 'Probable', 'choice contract'],
            ['Securities', 'সিকিউরিটিজ', 'Probable', 'financial papers'],
            ['Bonds', 'বন্ড', 'Probable', 'debt security'],
            ['Mutual fund', 'মিউচুয়াল ফান্ড', 'Probable', 'pooled investment'],
            ['Index', 'ইনডেক্স', 'Probable', 'market measure'],
        ];

        foreach (array_chunk($words2, 50) as $chunk) {
            DB::table('words')->insert(
                array_map(function ($w) use ($lesson2Id, $now) {
                    return [
                        'word' => $w[0], 'meaning' => $w[1], 'synonyms' => $w[2], 'antonyms' => $w[3],
                        'lesson_id' => $lesson2Id, 'status' => true, 'created_at' => $now, 'updated_at' => $now,
                    ];
                }, $chunk)
            );
        }

        // LESSON 3
        $lesson3Id = DB::table('lessons')->insertGetId([
            'title'      => 'আর্থিক বিষয় — Banking Excellence',
            'type'       => 'vocabulary',
            'chapter_id' => $chapterId,
            'status'     => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $words3 = [
            ['Economics', 'অর্থনীতি', 'Finance', 'wealth science'],
            ['Economy', 'অর্থব্যবস্থা', 'Finance', 'financial system'],
            ['Finance', 'অর্থায়ন', 'Finance', 'money management'],
            ['Financial', 'আর্থিক', 'Finance', 'money related'],
            ['Market', 'বাজার', 'Finance', 'place of trade'],
            ['Price', 'মূল্য', 'Finance', 'cost'],
            ['Value', 'মূল্য', 'Finance', 'worth'],
            ['Supply', 'সরবরাহ', 'Finance', 'available quantity'],
            ['Demand', 'চাহিদা', 'Finance', 'desired quantity'],
            ['Shortage', 'অভাব', 'Finance', 'insufficient supply'],
            ['Surplus', 'অতিরিক্ত', 'Finance', 'excess supply'],
            ['Competition', 'প্রতিযোগিতা', 'Finance', 'rivalry'],
            ['Monopoly', 'একচেটিয়া', 'Finance', 'sole control'],
            ['Efficiency', 'দক্ষতা', 'Finance', 'working well'],
            ['Productivity', 'উৎপাদনশীলতা', 'Finance', 'output'],
            ['Profitability', 'লাভজনকতা', 'Finance', 'profit ability'],
            ['Growth', 'বৃদ্ধি', 'Finance', 'increase'],
            ['Expansion', 'সম্প্রসারণ', 'Finance', 'enlargement'],
            ['Contraction', 'সংকুচন', 'Finance', 'shrinkage'],
            ['Cycle', 'চক্র', 'Finance', 'recurring pattern'],
            ['Trend', 'প্রবণতা', 'Finance', 'general direction'],
            ['Boom', 'উত্থান', 'Finance', 'period of growth'],
            ['Bust', 'পতন', 'Finance', 'period of decline'],
            ['Upturn', 'উত্থান', 'Finance', 'upward turn'],
            ['Downturn', 'হ্রাস', 'Finance', 'downward turn'],
            ['Fluctuation', 'ওঠানামা', 'Finance', 'up and down'],
            ['Volatility', 'অস্থিরতা', 'Finance', 'price variation'],
            ['Stability', 'স্থিতিশীলতা', 'Finance', 'unchanging'],
            ['Risk', 'ঝুঁকি', 'Finance', 'chance of loss'],
            ['Uncertainty', 'অনিশ্চয়তা', 'Finance', 'unknown outcome'],
            ['Model', 'মডেল', 'Finance', 'representation'],
            ['Forecast', 'পূর্বাভাস', 'Finance', 'prediction'],
            ['Projection', 'প্রজেকশন', 'Finance', 'estimation'],
            ['Allocation', 'বরাদ্দ', 'Finance', 'distribution'],
            ['Optimization', 'সর্বোত্তমকরণ', 'Finance', 'making best'],
        ];

        foreach (array_chunk($words3, 50) as $chunk) {
            DB::table('words')->insert(
                array_map(function ($w) use ($lesson3Id, $now) {
                    return [
                        'word' => $w[0], 'meaning' => $w[1], 'synonyms' => $w[2], 'antonyms' => $w[3],
                        'lesson_id' => $lesson3Id, 'status' => true, 'created_at' => $now, 'updated_at' => $now,
                    ];
                }, $chunk)
            );
        }

        // LESSON 4
        $lesson4Id = DB::table('lessons')->insertGetId([
            'title'      => 'ডিজিটাল ব্যাংকিং — Modern Finance',
            'type'       => 'vocabulary',
            'chapter_id' => $chapterId,
            'status'     => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $words4 = [
            ['Digital', 'ডিজিটাল', 'Digital', 'computer-based'],
            ['Technology', 'প্রযুক্তি', 'Digital', 'scientific tool'],
            ['Innovation', 'উদ্ভাবন', 'Digital', 'new idea'],
            ['Platform', 'প্ল্যাটফর্ম', 'Digital', 'operating base'],
            ['System', 'সিস্টেম', 'Digital', 'organized structure'],
            ['Software', 'সফটওয়্যার', 'Digital', 'computer program'],
            ['Hardware', 'হার্ডওয়্যার', 'Digital', 'physical equipment'],
            ['Network', 'নেটওয়ার্ক', 'Digital', 'connected system'],
            ['Server', 'সার্ভার', 'Digital', 'central computer'],
            ['Mobile', 'মোবাইল', 'Digital', 'portable phone'],
            ['App', 'অ্যাপ', 'Digital', 'short application'],
            ['Interface', 'ইন্টারফেস', 'Digital', 'connection point'],
            ['Dashboard', 'ড্যাশবোর্ড', 'Digital', 'control panel'],
            ['Menu', 'মেনু', 'Digital', 'selection list'],
            ['Upload', 'আপলোড', 'Digital', 'send to server'],
            ['Download', 'ডাউনলোড', 'Digital', 'receive from server'],
            ['File', 'ফাইল', 'Digital', 'data container'],
            ['Storage', 'সংরক্ষণ', 'Digital', 'data keeping'],
            ['Memory', 'মেমোরি', 'Digital', 'data area'],
            ['Database', 'ডাটাবেস', 'Digital', 'data collection'],
            ['Encryption', 'এনক্রিপশন', 'Digital', 'code protection'],
            ['Login', 'লগইন', 'Digital', 'enter system'],
            ['Logout', 'লগআউট', 'Digital', 'exit system'],
            ['Password', 'পাসওয়ার্ড', 'Digital', 'secret word'],
            ['Verification', 'যাচাইকরণ', 'Digital', 'checking'],
            ['Authentication', 'প্রমাণীকরণ', 'Digital', 'proof of identity'],
            ['Authorization', 'অনুমোদন', 'Digital', 'permission'],
            ['Notification', 'বিজ্ঞপ্তি', 'Digital', 'alert message'],
            ['Cybersecurity', 'সাইবার নিরাপত্তা', 'Digital', 'data protection'],
            ['Blockchain', 'ব্লকচেইন', 'Digital', 'distributed ledger'],
            ['Cryptocurrency', 'ক্রিপ্টোকারেন্সি', 'Digital', 'digital currency'],
            ['Bitcoin', 'বিটকয়েন', 'Digital', 'digital coin'],
            ['Fintech', 'ফিনটেক', 'Digital', 'financial technology'],
            ['API', 'এপিআই', 'Digital', 'program connection'],
            ['Cloud', 'ক্লাউড', 'Digital', 'internet storage'],
            ['Analytics', 'বিশ্লেষণ', 'Digital', 'data analysis'],
            ['Automation', 'স্বয়ংক্রিয়করণ', 'Digital', 'automatic operation'],
            ['Machine learning', 'মেশিন লার্নিং', 'Digital', 'algorithm learning'],
            ['Big data', 'বড় ডেটা', 'Digital', 'large data set'],
            ['Performance', 'কর্মক্ষমতা', 'Digital', 'how well working'],
        ];

        foreach (array_chunk($words4, 50) as $chunk) {
            DB::table('words')->insert(
                array_map(function ($w) use ($lesson4Id, $now) {
                    return [
                        'word' => $w[0], 'meaning' => $w[1], 'synonyms' => $w[2], 'antonyms' => $w[3],
                        'lesson_id' => $lesson4Id, 'status' => true, 'created_at' => $now, 'updated_at' => $now,
                    ];
                }, $chunk)
            );
        }
    }

    public function down(): void
    {
        //
    }
};
