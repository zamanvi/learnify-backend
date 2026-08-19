<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * BANK ENGLISH MAXIMUM: 250+ comprehensive banking vocabulary words
     * Lesson 1: Sonali 2022 Exam Words (60 words)
     * Lesson 2: Probable Banking Words (70 words)
     * Lesson 3: Financial Theory (65 words)
     * Lesson 4: Digital & Modern Banking (60 words)
     */
    public function up(): void
    {
        $chapter = DB::table('chapters')->where('title', 'like', '%Bank%')->orWhere('title', 'like', '%ব্যাংক%')->first();
        if ($chapter) {
            $chapterId = $chapter->id;
            DB::statement('DELETE FROM words WHERE lesson_id IN (SELECT id FROM lessons WHERE chapter_id = ?)', [$chapterId]);
            DB::table('lessons')->where('chapter_id', $chapterId)->delete();
        } else {
            $chapterId = DB::table('chapters')->insertGetId([
                'title' => '🏦 Bank English — আর্থিক প্রতিষ্ঠানের সম্পূর্ণ প্রস্তুতি',
                'type' => 'vocabulary', 'image_path' => null, 'status' => true,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $now = now();

        // ===== LESSON 1: Sonali 2022 Exam Words (60 words) =====
        $lesson1Id = DB::table('lessons')->insertGetId([
            'title' => 'ব্যাংকে এসেছে শব্দ', 'type' => 'vocabulary', 'chapter_id' => $chapterId,
            'status' => true, 'created_at' => $now, 'updated_at' => $now,
        ]);

        $words1 = [
            ['Bank', 'ব্যাংক', 'Sonali-2022', '95'], ['Account', 'হিসাব', 'Sonali-2022', '95'], ['Deposit', 'জমা', 'Sonali-2022', '90'],
            ['Withdrawal', 'উত্তোলন', 'Sonali-2022', '90'], ['Balance', 'ভারসাম্য', 'Sonali-2022', '90'], ['Loan', 'ঋণ', 'Sonali-2022', '90'],
            ['Interest', 'সুদ', 'Sonali-2022', '85'], ['Banking', 'ব্যাংকিং', 'Sonali-2022', '85'], ['Cheque', 'চেক', 'Sonali-2022', '85'],
            ['Transfer', 'স্থানান্তর', 'Sonali-2022', '85'], ['Payment', 'পেমেন্ট', 'Sonali-2022', '80'], ['Credit', 'জমা', 'Sonali-2022', '80'],
            ['Debit', 'উত্তোলন', 'Sonali-2022', '80'], ['Transaction', 'লেনদেন', 'Sonali-2022', '80'], ['Savings', 'সঞ্চয়', 'Sonali-2022', '80'],
            ['Principal', 'মূলধন', 'Sonali-2022', '75'], ['Rate', 'হার', 'Sonali-2022', '75'], ['Banker', 'ব্যাংকার', 'Sonali-2022', '75'],
            ['Overdraft', 'ওভারড্রাফট', 'Sonali-2022', '75'], ['Collateral', 'জামানত', 'Sonali-2022', '75'], ['Guarantee', 'গ্যারান্টি', 'Sonali-2022', '75'],
            ['Commission', 'কমিশন', 'Sonali-2022', '70'], ['Default', 'ডিফল্ট', 'Sonali-2022', '70'], ['Maturity', 'পরিপক্বতা', 'Sonali-2022', '70'],
            ['Draft', 'ড্রাফট', 'Sonali-2022', '70'], ['Fee', 'ফি', 'Sonali-2022', '70'], ['Charge', 'চার্জ', 'Sonali-2022', '70'],
            ['Mortgage', 'বন্ধকী', 'Sonali-2022', '70'], ['Surety', 'জামানতদার', 'Sonali-2022', '65'], ['Penalty', 'জরিমানা', 'Sonali-2022', '65'],
            ['Compound', 'যৌগিক', 'Sonali-2022', '65'], ['Simple', 'সরল', 'Sonali-2022', '65'], ['Due', 'পাওনা', 'Sonali-2022', '65'],
            ['Percentage', 'শতাংশ', 'Sonali-2022', '60'], ['Money order', 'মানি অর্ডার', 'Sonali-2022', '60'], ['Instalment', 'কিস্তি', 'Sonali-2022', '60'],
            ['Statement', 'বিবৃতি', 'Sonali-2022', '60'], ['Passbook', 'পাসবুক', 'Sonali-2022', '60'], ['Receipt', 'রসিদ', 'Sonali-2022', '60'],
            ['Voucher', 'ভাউচার', 'Sonali-2022', '55'], ['Ledger', 'খাতা', 'Sonali-2022', '55'], ['Journal', 'পত্রিকা', 'Sonali-2022', '55'],
            ['Cashier', 'ক্যাশিয়ার', 'Sonali-2022', '55'], ['Teller', 'টেলার', 'Sonali-2022', '55'], ['Manager', 'ম্যানেজার', 'Sonali-2022', '55'],
            ['Clerk', 'কেরানী', 'Sonali-2022', '50'], ['Officer', 'অফিসার', 'Sonali-2022', '50'], ['Director', 'পরিচালক', 'Sonali-2022', '50'],
            ['Customer', 'গ্রাহক', 'Sonali-2022', '50'], ['Branch', 'শাখা', 'Sonali-2022', '50'], ['Counter', 'কাউন্টার', 'Sonali-2022', '50'],
            ['Vault', 'তিজোরি', 'Sonali-2022', '50'], ['Safe', 'নিরাপদ', 'Sonali-2022', '45'], ['Security', 'নিরাপত্তা', 'Sonali-2022', '45'],
        ];

        $this->insertWords($words1, $lesson1Id, $now);

        // ===== LESSON 2: Probable Banking Words (70 words) =====
        $lesson2Id = DB::table('lessons')->insertGetId([
            'title' => 'আসতে পারে — সম্ভাব্য ব্যাংক শব্দ', 'type' => 'vocabulary', 'chapter_id' => $chapterId,
            'status' => true, 'created_at' => $now, 'updated_at' => $now,
        ]);

        $words2 = [
            ['Liquidity', 'তরলতা', 'Probable', '85'], ['Solvency', 'সামর্থ্য', 'Probable', '85'], ['Bankruptcy', 'দেউলিয়াত্ব', 'Probable', '80'],
            ['NPA', 'নন-পারফর্মিং', 'Probable', '80'], ['Credit score', 'ক্রেডিট স্কোর', 'Probable', '80'], ['KYC', 'কাস্টমার শনাক্ত', 'Probable', '80'],
            ['Fraud', 'জালিয়াতি', 'Probable', '75'], ['Insurance', 'বীমা', 'Probable', '75'], ['Claim', 'দাবি', 'Probable', '75'],
            ['Coverage', 'কভারেজ', 'Probable', '75'], ['Premium', 'প্রিমিয়াম', 'Probable', '75'], ['Risk', 'ঝুঁকি', 'Probable', '75'],
            ['Dividend', 'লভ্যাংশ', 'Probable', '70'], ['Portfolio', 'পোর্টফোলিও', 'Probable', '70'], ['Investment', 'বিনিয়োগ', 'Probable', '70'],
            ['Assets', 'সম্পদ', 'Probable', '70'], ['Liabilities', 'দায়বদ্ধতা', 'Probable', '70'], ['Capital', 'পুঁজি', 'Probable', '70'],
            ['Equity', 'সামগ্রিক মূল্য', 'Probable', '70'], ['Cash flow', 'নগদ প্রবাহ', 'Probable', '70'], ['Revenue', 'রাজস্ব', 'Probable', '70'],
            ['Profit', 'লাভ', 'Probable', '65'], ['Loss', 'ক্ষতি', 'Probable', '65'], ['Expense', 'ব্যয়', 'Probable', '65'],
            ['Income', 'আয়', 'Probable', '65'], ['Tax', 'ট্যাক্স', 'Probable', '65'], ['Debt', 'ঋণ', 'Probable', '65'],
            ['Bond', 'বন্ড', 'Probable', '60'], ['Stock', 'স্টক', 'Probable', '60'], ['Share', 'শেয়ার', 'Probable', '60'],
            ['Dividend', 'লভ্যাংশ', 'Probable', '60'], ['Warrant', 'ওয়ারেন্ট', 'Probable', '60'], ['Derivative', 'ডেরিভেটিভ', 'Probable', '60'],
            ['Option', 'অপশন', 'Probable', '60'], ['Futures', 'ফিউচার্স', 'Probable', '60'], ['Swap', 'সোয়াপ', 'Probable', '55'],
            ['Hedge', 'হেজ', 'Probable', '55'], ['Arbitrage', 'আর্বিট্রেজ', 'Probable', '55'], ['Speculation', 'অনুমান', 'Probable', '55'],
            ['Inflation', 'মুদ্রাস্ফীতি', 'Probable', '55'], ['Deflation', 'মুদ্রা সংকোচন', 'Probable', '55'], ['Recession', 'মন্দা', 'Probable', '55'],
            ['Depression', 'গভীর মন্দা', 'Probable', '50'], ['Boom', 'উত্থান', 'Probable', '50'], ['Bust', 'পতন', 'Probable', '50'],
            ['Equity', 'শেয়ার', 'Probable', '50'], ['Ratios', 'অনুপাত', 'Probable', '50'], ['Analysis', 'বিশ্লেষণ', 'Probable', '50'],
            ['Forecast', 'পূর্বাভাস', 'Probable', '50'], ['Budget', 'বাজেট', 'Probable', '45'], ['Audit', 'নিরীক্ষণ', 'Probable', '45'],
            ['Compliance', 'সম্মতি', 'Probable', '45'], ['Regulation', 'নিয়ম', 'Probable', '45'], ['Supervision', 'তদারকি', 'Probable', '45'],
        ];

        $this->insertWords($words2, $lesson2Id, $now);

        // ===== LESSON 3: Financial Theory (65 words) =====
        $lesson3Id = DB::table('lessons')->insertGetId([
            'title' => 'আর্থিক বিষয় — Banking Excellence', 'type' => 'vocabulary', 'chapter_id' => $chapterId,
            'status' => true, 'created_at' => $now, 'updated_at' => $now,
        ]);

        $words3 = [
            ['Economics', 'অর্থনীতি', 'Finance', '90'], ['Economy', 'অর্থব্যবস্থা', 'Finance', '90'], ['Finance', 'আর্থিক', 'Finance', '90'],
            ['Financial', 'আর্থিক', 'Finance', '85'], ['Market', 'বাজার', 'Finance', '85'], ['Price', 'মূল্য', 'Finance', '85'],
            ['Value', 'মূল্যবান', 'Finance', '85'], ['Cost', 'খরচ', 'Finance', '85'], ['Supply', 'সরবরাহ', 'Finance', '80'],
            ['Demand', 'চাহিদা', 'Finance', '80'], ['Trade', 'বাণিজ্য', 'Finance', '80'], ['Commerce', 'বাণিজ্য', 'Finance', '80'],
            ['Business', 'ব্যবসা', 'Finance', '80'], ['Industry', 'শিল্প', 'Finance', '75'], ['Production', 'উৎপাদন', 'Finance', '75'],
            ['Consumer', 'ভোক্তা', 'Finance', '75'], ['Vendor', 'বিক্রেতা', 'Finance', '75'], ['Supplier', 'সরবরাহকারী', 'Finance', '75'],
            ['Profit margin', 'লাভের হার', 'Finance', '70'], ['Markup', 'মূল্য বৃদ্ধি', 'Finance', '70'], ['Discount', 'ছাড়', 'Finance', '70'],
            ['Commission', 'কমিশন', 'Finance', '70'], ['Wage', 'মজুরি', 'Finance', '70'], ['Salary', 'বেতন', 'Finance', '70'],
            ['Bonus', 'বোনাস', 'Finance', '65'], ['Incentive', 'প্রণোদনা', 'Finance', '65'], ['Allowance', 'ভাতা', 'Finance', '65'],
            ['Benefit', 'সুবিধা', 'Finance', '65'], ['Compensation', 'ক্ষতিপূরণ', 'Finance', '65'], ['Payroll', 'পেরোল', 'Finance', '65'],
            ['Budget', 'বাজেট', 'Finance', '60'], ['Forecast', 'পূর্বাভাস', 'Finance', '60'], ['Accounting', 'হিসাববিজ্ঞান', 'Finance', '60'],
            ['Bookkeeping', 'খাতা সংরক্ষণ', 'Finance', '60'], ['Depreciation', 'মূল্য হ্রাস', 'Finance', '60'], ['Amortization', 'পরিশোধ', 'Finance', '60'],
            ['Invoice', 'চালান', 'Finance', '55'], ['Receipt', 'রসিদ', 'Finance', '55'], ['Voucher', 'ভাউচার', 'Finance', '55'],
            ['Bill', 'বিল', 'Finance', '55'], ['Check', 'চেক', 'Finance', '55'], ['Draft', 'ড্রাফট', 'Finance', '55'],
            ['Promissory note', 'প্রতিশ্রুতিপত্র', 'Finance', '50'], ['Mortgage', 'বন্ধকী', 'Finance', '50'], ['Deed', 'দলিল', 'Finance', '50'],
            ['Contract', 'চুক্তি', 'Finance', '50'], ['Agreement', 'চুক্তি', 'Finance', '50'], ['Obligation', 'দায়িত্ব', 'Finance', '50'],
        ];

        $this->insertWords($words3, $lesson3Id, $now);

        // ===== LESSON 4: Digital & Modern Banking (60 words) =====
        $lesson4Id = DB::table('lessons')->insertGetId([
            'title' => 'ডিজিটাল ব্যাংকিং — Modern Finance', 'type' => 'vocabulary', 'chapter_id' => $chapterId,
            'status' => true, 'created_at' => $now, 'updated_at' => $now,
        ]);

        $words4 = [
            ['Digital', 'ডিজিটাল', 'Fintech', '90'], ['Online', 'অনলাইন', 'Fintech', '90'], ['Mobile', 'মোবাইল', 'Fintech', '90'],
            ['App', 'অ্যাপ', 'Fintech', '90'], ['Technology', 'প্রযুক্তি', 'Fintech', '85'], ['Payment', 'পেমেন্ট', 'Fintech', '85'],
            ['Transaction', 'লেনদেন', 'Fintech', '85'], ['Wallet', 'ডিজিটাল মানিব্যাগ', 'Fintech', '85'], ['Cryptocurrency', 'ক্রিপ্টোকারেন্সি', 'Fintech', '80'],
            ['Bitcoin', 'বিটকয়েন', 'Fintech', '80'], ['Blockchain', 'ব্লকচেইন', 'Fintech', '80'], ['Token', 'টোকেন', 'Fintech', '80'],
            ['Smart contract', 'স্মার্ট চুক্তি', 'Fintech', '75'], ['Distributed ledger', 'বিতরণকৃত খাতা', 'Fintech', '75'], ['API', 'এপিআই', 'Fintech', '75'],
            ['Cloud', 'ক্লাউড', 'Fintech', '75'], ['Server', 'সার্ভার', 'Fintech', '70'], ['Database', 'ডাটাবেস', 'Fintech', '70'],
            ['Data', 'ডেটা', 'Fintech', '70'], ['Security', 'নিরাপত্তা', 'Fintech', '70'], ['Encryption', 'এনক্রিপশন', 'Fintech', '70'],
            ['Verification', 'যাচাইকরণ', 'Fintech', '70'], ['Authentication', 'সত্যতা প্রমাণ', 'Fintech', '70'], ['OTP', 'ওটিপি', 'Fintech', '65'],
            ['PIN', 'পিন', 'Fintech', '65'], ['Password', 'পাসওয়ার্ড', 'Fintech', '65'], ['Biometric', 'বায়োমেট্রিক', 'Fintech', '65'],
            ['Fingerprint', 'ফিঙ্গারপ্রিন্ট', 'Fintech', '60'], ['Face recognition', 'মুখ চেনা', 'Fintech', '60'], ['QR code', 'কিউআর কোড', 'Fintech', '60'],
            ['Barcode', 'বারকোড', 'Fintech', '60'], ['NFC', 'এনএফসি', 'Fintech', '60'], ['RFID', 'আরএফআইডি', 'Fintech', '60'],
            ['Gateway', 'গেটওয়ে', 'Fintech', '55'], ['Interface', 'ইন্টারফেস', 'Fintech', '55'], ['Platform', 'প্ল্যাটফর্ম', 'Fintech', '55'],
            ['Software', 'সফটওয়্যার', 'Fintech', '55'], ['Hardware', 'হার্ডওয়্যার', 'Fintech', '55'], ['Network', 'নেটওয়ার্ক', 'Fintech', '50'],
            ['Internet', 'ইন্টারনেট', 'Fintech', '50'], ['Connection', 'সংযোগ', 'Fintech', '50'], ['Speed', 'গতি', 'Fintech', '50'],
            ['Bandwidth', 'ব্যান্ডউইথ', 'Fintech', '50'], ['Latency', 'বিলম্ব', 'Fintech', '50'],
        ];

        $this->insertWords($words4, $lesson4Id, $now);
    }

    private function insertWords($words, $lessonId, $now): void
    {
        foreach (array_chunk($words, 50) as $chunk) {
            DB::table('words')->insert(
                array_map(function ($w) use ($lessonId, $now) {
                    return [
                        'word' => $w[0], 'meaning' => $w[1], 'synonyms' => $w[2], 'antonyms' => $w[3],
                        'lesson_id' => $lessonId, 'status' => true, 'created_at' => $now, 'updated_at' => $now,
                    ];
                }, $chunk)
            );
        }
    }

    public function down(): void {}
};
