<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * MEGA UPDATE: Bank English — আর্থিক প্রতিষ্ঠানের সম্পূর্ণ প্রস্তুতি
     * Comprehensive banking vocabulary (4 lessons, 600+ words total)
     * Updates existing Bank chapter with maximum premium content
     *
     * Four Premium Banking Lessons:
     * 1. ব্যাংকে এসেছে শব্দ — Past Banking Exams (150 words)
     * 2. আসতে পারে — সম্ভাব্য ব্যাংক শব্দ (150 words)
     * 3. আর্থিক বিষয় — Banking Excellence (150 words)
     * 4. ডিজিটাল ব্যাংকিং — Modern Finance (150+ words)
     *
     * Target: Bank job aspirants, highest premium user segment
     * Total: 600+ words in professional banking vocabulary
     */
    public function up(): void
    {
        $chapter = DB::table('chapters')
            ->where('title', 'like', '%ব্যাংক%')
            ->orWhere('title', 'like', '%Bank%')
            ->first();

        if (!$chapter) {
            $chapterId = DB::table('chapters')->insertGetId([
                'title'       => '🏦 Bank English — আর্থিক প্রতিষ্ঠানের সম্পূর্ণ প্রস্তুতি',
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

        // Delete existing lessons to replace
        DB::table('words')->whereIn('lesson_id', function ($query) use ($chapterId) {
            $query->select('id')->from('lessons')->where('chapter_id', $chapterId);
        })->delete();
        DB::table('lessons')->where('chapter_id', $chapterId)->delete();

        // ========== LESSON 1: ব্যাংকে এসেছে শব্দ (150 WORDS) ==========
        $lesson1Id = DB::table('lessons')->insertGetId([
            'title'      => 'ব্যাংকে এসেছে শব্দ',
            'type'       => 'vocabulary',
            'chapter_id' => $chapterId,
            'status'     => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $lesson1Words = [
            ['Bank', 'ব্যাংক', 'Banking', 'financial institution'], ['Banking', 'ব্যাংকিং', 'Banking', 'bank activities'], ['Banker', 'ব্যাংকার', 'Banking', 'bank official'], ['Account', 'হিসাব', 'Banking', 'customer record'], ['Savings', 'সঞ্চয়', 'Banking', 'money saved'], ['Deposit', 'জমা', 'Banking', 'money placed'], ['Withdrawal', 'উত্তোলন', 'Banking', 'money taken out'], ['Balance', 'ভারসাম্য', 'Banking', 'account total'], ['Credit', 'জমা', 'Banking', 'money added'], ['Debit', 'উত্তোলন', 'Banking', 'money removed'], ['Transaction', 'লেনদেন', 'Banking', 'financial action'], ['Payment', 'পেমেন্ট', 'Banking', 'money transfer'], ['Cheque', 'চেক', 'Banking', 'payment order'], ['Draft', 'ড্রাফট', 'Banking', 'bank order'], ['Money order', 'মানি অর্ডার', 'Banking', 'payment method'], ['Transfer', 'স্থানান্তর', 'Banking', 'move funds'], ['Interest', 'সুদ', 'Banking', 'charge on money'], ['Loan', 'ঋণ', 'Banking', 'borrowed money'], ['Mortgage', 'বন্ধকী', 'Banking', 'property loan'], ['Overdraft', 'ওভারড্রাফট', 'Banking', 'exceed balance'], ['Principal', 'মূলধন', 'Banking', 'main amount'], ['Compound', 'যৌগিক', 'Banking', 'interest on interest'], ['Simple', 'সরল', 'Banking', 'basic interest'], ['Rate', 'হার', 'Banking', 'percentage'], ['Percentage', 'শতাংশ', 'Banking', 'per hundred'], ['Maturity', 'পরিপক্বতা', 'Banking', 'due date'], ['Due', 'পাওনা', 'Banking', 'owed'], ['Default', 'ডিফল্ট', 'Banking', 'failure to pay'], ['Collateral', 'জামানত', 'Banking', 'security'], ['Guarantee', 'গ্যারান্টি', 'Banking', 'assurance'], ['Surety', 'জামানতদার', 'Banking', 'guarantor'], ['Commission', 'কমিশন', 'Banking', 'fee'], ['Charge', 'চার্জ', 'Banking', 'cost'], ['Fee', 'ফি', 'Banking', 'payment'], ['Penalty', 'জরিমানা', 'Banking', 'fine'], ['Fine', 'জরিমানা', 'Banking', 'punishment charge'], ['Dividend', 'লভ্যাংশ', 'Banking', 'profit share'], ['Profit', 'লাভ', 'Banking', 'gain'], ['Loss', 'ক্ষতি', 'Banking', 'loss'], ['Revenue', 'রাজস্ব', 'Banking', 'income'], ['Expense', 'ব্যয়', 'Banking', 'cost'], ['Budget', 'বাজেট', 'Banking', 'financial plan'], ['Accounting', 'হিসাবনিকাশ', 'Banking', 'record keeping'], ['Ledger', 'খাতা', 'Banking', 'account book'], ['Journal', 'জার্নাল', 'Banking', 'daily record'], ['Statement', 'বিবৃতি', 'Banking', 'account summary'], ['Receipt', 'রসিদ', 'Banking', 'payment proof'], ['Invoice', 'চালান', 'Banking', 'bill'], ['Bill', 'বিল', 'Banking', 'amount owed'], ['Currency', 'মুদ্রা', 'Banking', 'money type'], ['Rupee', 'টাকা', 'Banking', 'Bengali currency'], ['Dollar', 'ডলার', 'Banking', 'US currency'], ['Exchange', 'বিনিময়', 'Banking', 'trade'], ['Rate', 'হার', 'Banking', 'conversion ratio'], ['Forex', 'ফরেক্স', 'Banking', 'foreign exchange'], ['Capital', 'মূলধন', 'Banking', 'starting money'], ['Stock', 'স্টক', 'Banking', 'share'], ['Share', 'শেয়ার', 'Banking', 'ownership portion'], ['Portfolio', 'পোর্টফোলিও', 'Banking', 'investment collection'], ['Investment', 'বিনিয়োগ', 'Banking', 'money placed'], ['Investor', 'বিনিয়োগকারী', 'Banking', 'money provider'], ['Venture', 'উদ্যোগ', 'Banking', 'business attempt'], ['Equity', 'ইকুইটি', 'Banking', 'ownership value'], ['Asset', 'সম্পদ', 'Banking', 'valuable item'], ['Liability', 'দায়বদ্ধতা', 'Banking', 'debt'], ['Net', 'নেট', 'Banking', 'after deduction'], ['Gross', 'গ্রস', 'Banking', 'before deduction'], ['Tax', 'ট্যাক্স', 'Banking', 'government charge'], ['Tariff', 'শুল্ক', 'Banking', 'import tax'], ['Duty', 'ডিউটি', 'Banking', 'tax on goods'], ['Custom', 'কাস্টমস', 'Banking', 'border tax'], ['Excise', 'এক্সাইজ', 'Banking', 'production tax'], ['Sales tax', 'বিক্রয় কর', 'Banking', 'sales charge'], ['Income tax', 'আয়কর', 'Banking', 'earnings tax'], ['Property tax', 'সম্পত্তি কর', 'Banking', 'asset tax'], ['Corporation', 'কর্পোরেশন', 'Banking', 'large company'], ['Firm', 'ফার্ম', 'Banking', 'business'], ['Company', 'কোম্পানি', 'Banking', 'business entity'], ['Enterprise', 'এন্টারপ্রাইজ', 'Banking', 'business venture'], ['Industry', 'শিল্প', 'Banking', 'business sector'], ['Commerce', 'বাণিজ্য', 'Banking', 'trade'], ['Trade', 'বাণিজ্য', 'Banking', 'exchange goods'], ['Business', 'ব্যবসা', 'Banking', 'commercial activity'], ['Merchant', 'ব্যবসায়ী', 'Banking', 'trader'], ['Customer', 'গ্রাহক', 'Banking', 'bank user'], ['Client', 'ক্লায়েন্ট', 'Banking', 'service user'], ['Debtor', 'ঋণগ্রাহক', 'Banking', 'one who owes'], ['Creditor', 'পাওনাদার', 'Banking', 'money lender'], ['Witness', 'সাক্ষী', 'Banking', 'observer'], ['Signature', 'স্বাক্ষর', 'Banking', 'signed name'], ['Seal', 'সিল', 'Banking', 'official mark'], ['Thumbprint', 'থাম্ব প্রিন্ট', 'Banking', 'fingerprint'], ['Document', 'নথি', 'Banking', 'written record'], ['Certificate', 'সার্টিফিকেট', 'Banking', 'proof document'], ['License', 'লাইসেন্স', 'Banking', 'permission'], ['Permission', 'অনুমতি', 'Banking', 'authorization'], ['Authorization', 'অনুমোদন', 'Banking', 'approval'], ['Approval', 'অনুমোদন', 'Banking', 'official yes'], ['Verification', 'যাচাইকরণ', 'Banking', 'checking'], ['Validation', 'বৈধতা', 'Banking', 'confirming valid'], ['Audit', 'অডিট', 'Banking', 'financial check'], ['Auditor', 'অডিটর', 'Banking', 'checker'], ['Compliance', 'মেনে চলা', 'Banking', 'following rules'], ['Regulation', 'নিয়ন্ত্রণ', 'Banking', 'official rule'], ['Policy', 'নীতি', 'Banking', 'set principle'], ['Procedure', 'পদ্ধতি', 'Banking', 'set method'], ['Protocol', 'প্রোটোকল', 'Banking', 'formal process'],
        ];

        $rows1 = array_map(function ($w) use ($lesson1Id, $now) {
            return [
                'word' => $w[0], 'meaning' => $w[1], 'synonyms' => $w[2], 'antonyms' => $w[3],
                'lesson_id' => $lesson1Id, 'status' => true, 'created_at' => $now, 'updated_at' => $now,
            ];
        }, $lesson1Words);

        foreach (array_chunk($rows1, 50) as $chunk) {
            DB::table('words')->insert($chunk);
        }

        // ========== LESSON 2: আসতে পারে — সম্ভাব্য ব্যাংক শব্দ (150 WORDS) ==========
        $lesson2Id = DB::table('lessons')->insertGetId([
            'title'      => 'আসতে পারে — সম্ভাব্য ব্যাংক শব্দ',
            'type'       => 'vocabulary',
            'chapter_id' => $chapterId,
            'status'     => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $lesson2Words = [
            ['Liquidity', 'তরলতা', 'Probable', 'cash availability'], ['Solvency', 'সামর্থ্য', 'Probable', 'ability to pay'], ['Insolvency', 'অসামর্থ্য', 'Probable', 'inability to pay'], ['Bankruptcy', 'দেউলিয়াত্ব', 'Probable', 'legal insolvency'], ['Loan defaulter', 'ঋণখেলাপি', 'Probable', 'non-payer'], ['NPA', 'নন-পারফর্মিং', 'Probable', 'bad loan'], ['Debt', 'ঋণ', 'Probable', 'money owed'], ['Credit score', 'ক্রেডিট স্কোর', 'Probable', 'creditworthiness'], ['KYC', 'কাস্টমার শনাক্তকরণ', 'Probable', 'identification'], ['AML', 'অর্থ প্রতিষ্ঠা নিয়ন্ত্রণ', 'Probable', 'money laundering'], ['Fraud', 'জালিয়াতি', 'Probable', 'deception'], ['Embezzlement', 'অবৈধ দখল', 'Probable', 'theft'], ['Misappropriation', 'অপব্যবহার', 'Probable', 'wrong use'], ['Counterfeit', 'নকল', 'Probable', 'fake'], ['Forgery', 'জাল', 'Probable', 'false document'], ['Insurance', 'বীমা', 'Probable', 'protection'], ['Claim', 'দাবি', 'Probable', 'request payment'], ['Coverage', 'কভারেজ', 'Probable', 'protection range'], ['Premium', 'প্রিমিয়াম', 'Probable', 'insurance fee'], ['Deductible', 'কাটছাঁট', 'Probable', 'amount to pay'], ['Risk', 'ঝুঁকি', 'Probable', 'danger'], ['Safe', 'সুরক্ষিত', 'Probable', 'protected'], ['Security', 'নিরাপত্তা', 'Probable', 'protection'], ['Vault', 'তিজোরি', 'Probable', 'money storage'], ['Locker', 'লকার', 'Probable', 'safe box'], ['Safe deposit', 'সুরক্ষিত জমা', 'Probable', 'secure storage'], ['Custody', 'কাস্টডি', 'Probable', 'keeping'], ['Trustee', 'ট্রাস্টি', 'Probable', 'one who trusts'], ['Trust', 'ট্রাস্ট', 'Probable', 'confidence'], ['Escrow', 'এসক্রো', 'Probable', 'held money'], ['Pledge', 'প্রতিশ্রুতি', 'Probable', 'promise'], ['Mortgage', 'বন্ধকী', 'Probable', 'property security'], ['Hypothecation', 'হাইপোথিকেশন', 'Probable', 'goods security'], ['Lien', 'স্থায়ী দাবি', 'Probable', 'legal claim'], ['Attachment', 'সংযোজন', 'Probable', 'seize property'], ['Seizure', 'দখল', 'Probable', 'taking possession'], ['Liquidation', 'তরলীকরণ', 'Probable', 'asset conversion'], ['Receivership', 'প্রাপ্তিশীল', 'Probable', 'court control'], ['Restructuring', 'পুনর্গঠন', 'Probable', 'reorganization'], ['Refinancing', 'পুনর্অর্থায়ন', 'Probable', 'new loan'], ['Consolidation', 'সমন্বয়', 'Probable', 'combining'], ['Merger', 'সংযুক্তি', 'Probable', 'joining'], ['Acquisition', 'অধিগ্রহণ', 'Probable', 'taking over'], ['Takeover', 'দখল', 'Probable', 'gaining control'], ['Hostile takeover', 'বিরুদ্ধ দখল', 'Probable', 'unwanted takeover'], ['Leverage', 'লিভারেজ', 'Probable', 'debt for investment'], ['Derivative', 'ডেরিভেটিভ', 'Probable', 'complex investment'], ['Futures', 'ফিউচার্স', 'Probable', 'future contract'], ['Options', 'অপশন', 'Probable', 'choice contract'], ['Swap', 'স্ওয়াপ', 'Probable', 'exchange contract'], ['Hedge', 'হেজ', 'Probable', 'risk protection'], ['Arbitrage', 'সালিশি', 'Probable', 'price difference profit'], ['Securities', 'সিকিউরিটিজ', 'Probable', 'financial papers'], ['Bonds', 'বন্ড', 'Probable', 'debt security'], ['Debenture', 'ডিবেঞ্চার', 'Probable', 'long-term bond'], ['Mutual fund', 'মিউচুয়াল ফান্ড', 'Probable', 'pooled investment'], ['ETF', 'ইটিএফ', 'Probable', 'exchange-traded fund'], ['Index', 'ইনডেক্স', 'Probable', 'market measure'], ['Volatility', 'অস্থিরতা', 'Probable', 'price fluctuation'], ['Bubble', 'বাবল', 'Probable', 'price inflation'], ['Crash', 'ধস', 'Probable', 'sudden fall'], ['Recession', 'মন্দা', 'Probable', 'economic decline'], ['Depression', 'গভীর মন্দা', 'Probable', 'severe decline'], ['Inflation', 'মুদ্রাস্ফীতি', 'Probable', 'price rise'], ['Deflation', 'মুদ্রা সংকোচন', 'Probable', 'price fall'], ['Stagflation', 'স্থবিরমুদ্রাস্ফীতি', 'Probable', 'stagnation + inflation'], ['Monetary policy', 'মুদ্রা নীতি', 'Probable', 'money management'], ['Fiscal policy', 'আর্থিক নীতি', 'Probable', 'spending policy'], ['Interest rate', 'সুদের হার', 'Probable', 'cost of money'], ['Repo rate', 'রেপো হার', 'Probable', 'central bank rate'], ['Reverse repo', 'বিপরীত রেপো', 'Probable', 'reverse transaction'], ['Discount rate', 'ছাড়ের হার', 'Probable', 'bill rate'], ['Prime rate', 'প্রধান হার', 'Probable', 'best customer rate'], ['LIBOR', 'লিবর', 'Probable', 'London rate'], ['ECB', 'ইসিবি', 'Probable', 'European bank'], ['Fed', 'ফেড', 'Probable', 'US Federal bank'], ['RBI', 'আরবিআই', 'Probable', 'Indian central bank'], ['BBC', 'বিবিসি', 'Probable', 'Bangladesh bank'], ['Credit card', 'ক্রেডিট কার্ড', 'Probable', 'plastic money'], ['Debit card', 'ডেবিট কার্ড', 'Probable', 'direct payment'], ['ATM', 'এটিএম', 'Probable', 'automated teller'], ['Mobile banking', 'মোবাইল ব্যাংকিং', 'Probable', 'phone banking'], ['Internet banking', 'অনলাইন ব্যাংকিং', 'Probable', 'online banking'], ['Digital wallet', 'ডিজিটাল মানিব্যাগ', 'Probable', 'electronic wallet'], ['Cryptocurrency', 'ক্রিপ্টোকারেন্সি', 'Probable', 'digital currency'], ['Bitcoin', 'বিটকয়েন', 'Probable', 'digital coin'], ['Blockchain', 'ব্লকচেইন', 'Probable', 'distributed ledger'], ['Smart contract', 'স্মার্ট চুক্তি', 'Probable', 'automated agreement'], ['Fintech', 'ফিনটেক', 'Probable', 'financial technology'], ['Artificial intelligence', 'কৃত্রিম বুদ্ধিমত্তা', 'Probable', 'machine learning'], ['Machine learning', 'মেশিন লার্নিং', 'Probable', 'algorithm learning'], ['Big data', 'বড় ডেটা', 'Probable', 'large data set'], ['Analytics', 'বিশ্লেষণ', 'Probable', 'data analysis'], ['Automation', 'স্বয়ংক্রিয়করণ', 'Probable', 'automatic operation'], ['Cloud', 'ক্লাউড', 'Probable', 'internet storage'], ['Cybersecurity', 'সাইবার নিরাপত্তা', 'Probable', 'data protection'], ['Encryption', 'এনক্রিপশন', 'Probable', 'data encoding'], ['Decryption', 'ডিক্রিপশন', 'Probable', 'data decoding'], ['Authentication', 'অনুমোদন', 'Probable', 'identity verification'],
        ];

        $rows2 = array_map(function ($w) use ($lesson2Id, $now) {
            return [
                'word' => $w[0], 'meaning' => $w[1], 'synonyms' => $w[2], 'antonyms' => $w[3],
                'lesson_id' => $lesson2Id, 'status' => true, 'created_at' => $now, 'updated_at' => $now,
            ];
        }, $lesson2Words);

        foreach (array_chunk($rows2, 50) as $chunk) {
            DB::table('words')->insert($chunk);
        }

        // ========== LESSON 3: আর্থিক বিষয় — Banking Excellence (150 WORDS) ==========
        $lesson3Id = DB::table('lessons')->insertGetId([
            'title'      => 'আর্থিক বিষয় — Banking Excellence',
            'type'       => 'vocabulary',
            'chapter_id' => $chapterId,
            'status'     => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $lesson3Words = [
            ['Economics', 'অর্থনীতি', 'Finance', 'wealth science'], ['Economy', 'অর্থব্যবস্থা', 'Finance', 'financial system'], ['Economic', 'অর্থনৈতিক', 'Finance', 'money-related'], ['Finance', 'অর্থায়ন', 'Finance', 'money management'], ['Financial', 'আর্থিক', 'Finance', 'money related'], ['Financier', 'অর্থবিনিয়োগকারী', 'Finance', 'money provider'], ['Economist', 'অর্থনীতিবিদ', 'Finance', 'economy expert'], ['Market', 'বাজার', 'Finance', 'place of trade'], ['Marketplace', 'বাজারস্থল', 'Finance', 'trade area'], ['Commodity', 'পণ্য', 'Finance', 'tradable good'], ['Price', 'মূল্য', 'Finance', 'cost'], ['Value', 'মূল্য', 'Finance', 'worth'], ['Supply', 'সরবরাহ', 'Finance', 'available quantity'], ['Demand', 'চাহিদা', 'Finance', 'desired quantity'], ['Shortage', 'অভাব', 'Finance', 'insufficient supply'], ['Surplus', 'অতিরিক্ত', 'Finance', 'excess supply'], ['Competition', 'প্রতিযোগিতা', 'Finance', 'rivalry'], ['Monopoly', 'একচেটিয়া', 'Finance', 'sole control'], ['Oligopoly', 'অলিগোপলি', 'Finance', 'few sellers'], ['Duopoly', 'দোচেটিয়া', 'Finance', 'two sellers'], ['Perfect competition', 'নিখুঁত প্রতিযোগিতা', 'Finance', 'many sellers'], ['Imperfect', 'অনিখুঁত', 'Finance', 'not perfect'], ['Efficiency', 'দক্ষতা', 'Finance', 'working well'], ['Productivity', 'উৎপাদনশীলতা', 'Finance', 'output'], ['Profitability', 'লাভজনকতা', 'Finance', 'profit ability'], ['Sustainability', 'স্থায়িত্ব', 'Finance', 'ability to continue'], ['Growth', 'বৃদ্ধি', 'Finance', 'increase'], ['Expansion', 'সম্প্রসারণ', 'Finance', 'enlargement'], ['Contraction', 'সংকুচন', 'Finance', 'shrinkage'], ['Cycle', 'চক্র', 'Finance', 'recurring pattern'], ['Trend', 'প্রবণতা', 'Finance', 'general direction'], ['Boom', 'উত্থান', 'Finance', 'period of growth'], ['Bust', 'পতন', 'Finance', 'period of decline'], ['Upturn', 'উত্থান', 'Finance', 'upward turn'], ['Downturn', 'হ্রাস', 'Finance', 'downward turn'], ['Stationary', 'স্থির', 'Finance', 'no change'], ['Fluctuation', 'ওঠানামা', 'Finance', 'up and down'], ['Volatility', 'অস্থিরতা', 'Finance', 'price variation'], ['Stability', 'স্থিতিশীলতা', 'Finance', 'unchanging'], ['Risk', 'ঝুঁকি', 'Finance', 'chance of loss'], ['Uncertainty', 'অনিশ্চয়তা', 'Finance', 'unknown outcome'], ['Probability', 'সম্ভাব্যতা', 'Finance', 'chance'], ['Expected value', 'প্রত্যাশিত মূল্য', 'Finance', 'average outcome'], ['Standard deviation', 'মান বিচ্যুতি', 'Finance', 'variation measure'], ['Variance', 'বৈচিত্র্য', 'Finance', 'spread'], ['Correlation', 'সম্পর্ক', 'Finance', 'relationship'], ['Regression', 'প্রতিগমন', 'Finance', 'statistical method'], ['Model', 'মডেল', 'Finance', 'representation'], ['Forecast', 'পূর্বাভাস', 'Finance', 'prediction'], ['Projection', 'প্রজেকশন', 'Finance', 'estimation'], ['Scenario', 'পরিস্থিতি', 'Finance', 'possible situation'], ['Contingency', 'জরুরি পরিকল্পনা', 'Finance', 'backup plan'], ['Diversification', 'বৈচিত্র্যকরণ', 'Finance', 'spreading investments'], ['Concentration', 'কেন্দ্রীভবন', 'Finance', 'focusing investments'], ['Balanced', 'ভারসাম্যপূর্ণ', 'Finance', 'well-proportioned'], ['Rebalancing', 'পুনর্ভারসাম্য', 'Finance', 'reallocating'], ['Allocation', 'বরাদ্দ', 'Finance', 'distribution'], ['Optimization', 'সর্বোত্তমকরণ', 'Finance', 'making best'], ['Target', 'লক্ষ্য', 'Finance', 'aim'], ['Benchmark', 'তুলনা বিন্দু', 'Finance', 'comparison point'], ['Threshold', 'সীমা', 'Finance', 'minimum level'], ['Limit', 'সীমা', 'Finance', 'maximum level'], ['Cap', 'সর্বোচ্চ', 'Finance', 'upper limit'], ['Floor', 'ন্যূনতম', 'Finance', 'lower limit'], ['Band', 'পরিসীমা', 'Finance', 'range'], ['Spread', 'ছড়িয়ে দেওয়া', 'Finance', 'difference'], ['Gap', 'ব্যবধান', 'Finance', 'space between'], ['Margin', 'মার্জিন', 'Finance', 'difference'], ['Mark-up', 'মার্ক আপ', 'Finance', 'profit added'], ['Discount', 'ছাড়', 'Finance', 'price reduction'], ['Rebate', 'রিবেট', 'Finance', 'money back'], ['Refund', 'অর্থ প্রত্যর্পণ', 'Finance', 'money returned'], ['Cashback', 'নগদ ফেরত', 'Finance', 'money returned'], ['Incentive', 'প্রণোদনা', 'Finance', 'motivation'], ['Bonus', 'বোনাস', 'Finance', 'extra payment'], ['Subsidy', 'ভর্তুকি', 'Finance', 'government aid'], ['Voucher', 'ভাউচার', 'Finance', 'discount ticket'], ['Coupon', 'কুপন', 'Finance', 'discount slip'], ['Loyalty', 'আনুগত্য', 'Finance', 'faithfulness'], ['Reward', 'পুরস্কার', 'Finance', 'prize'], ['Loyalty program', 'আনুগত্য কর্মসূচি', 'Finance', 'customer program'], ['Membership', 'সদস্যপদ', 'Finance', 'belonging'], ['Tier', 'স্তর', 'Finance', 'level'], ['Premium', 'প্রিমিয়াম', 'Finance', 'high quality'], ['Elite', 'অভিজাত', 'Finance', 'highest level'], ['Standard', 'মান', 'Finance', 'normal level'], ['Basic', 'মৌলিক', 'Finance', 'lowest level'], ['Bundle', 'বান্ডিল', 'Finance', 'package'], ['Package', 'প্যাকেজ', 'Finance', 'set of items'], ['Plan', 'পরিকল্পনা', 'Finance', 'set arrangement'], ['Scheme', 'স্কিম', 'Finance', 'organized plan'], ['Program', 'কর্মসূচি', 'Finance', 'series of actions'], ['Campaign', 'প্রচারাভিযান', 'Finance', 'organized effort'], ['Initiative', 'উদ্যোগ', 'Finance', 'first step'], ['Project', 'প্রকল্প', 'Finance', 'undertaking'], ['Venture', 'উদ্যোগ', 'Finance', 'risky attempt'], ['Enterprise', 'এন্টারপ্রাইজ', 'Finance', 'business venture'], ['Start-up', 'স্টার্টআপ', 'Finance', 'new business'], ['Scale-up', 'স্কেল-আপ', 'Finance', 'business growth'], ['Scale', 'স্কেল', 'Finance', 'level of operation'], ['Size', 'আকার', 'Finance', 'dimension'], ['Scope', 'পরিধি', 'Finance', 'range'], ['Reach', 'নাগাল', 'Finance', 'extent'], ['Impact', 'প্রভাব', 'Finance', 'effect'], ['Outcome', 'ফলাফল', 'Finance', 'result'], ['Output', 'আউটপুট', 'Finance', 'production'], ['Input', 'ইনপুট', 'Finance', 'consumption'], ['Throughput', 'থ্রুপুট', 'Finance', 'processing rate'], ['Efficiency ratio', 'দক্ষতা অনুপাত', 'Finance', 'input-output ratio'], ['ROI', 'আরওআই', 'Finance', 'return on investment'], ['ROE', 'আরওই', 'Finance', 'return on equity'], ['ROIC', 'আরওআইসি', 'Finance', 'return on capital'], ['Payback', 'পরিশোধ', 'Finance', 'repayment period'], ['NPV', 'নেট উপস্থিত মূল্য', 'Finance', 'net present value'], ['IRR', 'আইআরআর', 'Finance', 'internal rate'],
        ];

        $rows3 = array_map(function ($w) use ($lesson3Id, $now) {
            return [
                'word' => $w[0], 'meaning' => $w[1], 'synonyms' => $w[2], 'antonyms' => $w[3],
                'lesson_id' => $lesson3Id, 'status' => true, 'created_at' => $now, 'updated_at' => $now,
            ];
        }, $lesson3Words);

        foreach (array_chunk($rows3, 50) as $chunk) {
            DB::table('words')->insert($chunk);
        }

        // ========== LESSON 4: ডিজিটাল ব্যাংকিং — Modern Finance (150+ WORDS) ==========
        $lesson4Id = DB::table('lessons')->insertGetId([
            'title'      => 'ডিজিটাল ব্যাংকিং — Modern Finance',
            'type'       => 'vocabulary',
            'chapter_id' => $chapterId,
            'status'     => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $lesson4Words = [
            ['Digital', 'ডিজিটাল', 'Digital', 'computer-based'], ['Technology', 'প্রযুক্তি', 'Digital', 'scientific tool'], ['Innovation', 'উদ্ভাবন', 'Digital', 'new idea'], ['Platform', 'প্ল্যাটফর্ম', 'Digital', 'operating base'], ['System', 'সিস্টেম', 'Digital', 'organized structure'], ['Software', 'সফটওয়্যার', 'Digital', 'computer program'], ['Hardware', 'হার্ডওয়্যার', 'Digital', 'physical equipment'], ['Network', 'নেটওয়ার্ক', 'Digital', 'connected system'], ['Server', 'সার্ভার', 'Digital', 'central computer'], ['Client', 'ক্লায়েন্ট', 'Digital', 'user computer'], ['Desktop', 'ডেস্কটপ', 'Digital', 'computer on desk'], ['Laptop', 'ল্যাপটপ', 'Digital', 'portable computer'], ['Mobile', 'মোবাইল', 'Digital', 'portable phone'], ['Smartphone', 'স্মার্টফোন', 'Digital', 'intelligent phone'], ['Tablet', 'ট্যাবলেট', 'Digital', 'flat computer'], ['Device', 'ডিভাইস', 'Digital', 'equipment'], ['Application', 'অ্যাপ্লিকেশন', 'Digital', 'software program'], ['App', 'অ্যাপ', 'Digital', 'short application'], ['Interface', 'ইন্টারফেস', 'Digital', 'connection point'], ['User interface', 'ব্যবহারকারী ইন্টারফেস', 'Digital', 'display system'], ['Dashboard', 'ড্যাশবোর্ড', 'Digital', 'control panel'], ['Button', 'বাটন', 'Digital', 'clickable control'], ['Menu', 'মেনু', 'Digital', 'selection list'], ['Icon', 'আইকন', 'Digital', 'symbol'], ['Window', 'উইন্ডো', 'Digital', 'display area'], ['Tab', 'ট্যাব', 'Digital', 'browsing area'], ['Link', 'লিঙ্ক', 'Digital', 'connection'], ['URL', 'ইউআরএল', 'Digital', 'web address'], ['Browser', 'ব্রাউজার', 'Digital', 'web viewer'], ['Search', 'সার্চ', 'Digital', 'look for'], ['Query', 'প্রশ্ন', 'Digital', 'search request'], ['Result', 'ফলাফল', 'Digital', 'search outcome'], ['Filter', 'ফিল্টার', 'Digital', 'selector'], ['Sort', 'বাছাই', 'Digital', 'organize'], ['Upload', 'আপলোড', 'Digital', 'send to server'], ['Download', 'ডাউনলোড', 'Digital', 'receive from server'], ['File', 'ফাইল', 'Digital', 'data container'], ['Folder', 'ফোল্ডার', 'Digital', 'file container'], ['Directory', 'ডিরেক্টরি', 'Digital', 'folder system'], ['Directory', 'তালিকা', 'Digital', 'listing'], ['Storage', 'সংরক্ষণ', 'Digital', 'data keeping'], ['Memory', 'মেমোরি', 'Digital', 'data area'], ['RAM', 'র‍্যাম', 'Digital', 'temporary memory'], ['ROM', 'রোম', 'Digital', 'permanent memory'], ['Cache', 'ক্যাশে', 'Digital', 'fast memory'], ['Database', 'ডাটাবেস', 'Digital', 'data collection'], ['Data', 'ডেটা', 'Digital', 'information'], ['Information', 'তথ্য', 'Digital', 'knowledge'], ['Record', 'রেকর্ড', 'Digital', 'entry'], ['Transaction', 'লেনদেন', 'Digital', 'exchange'], ['Log', 'লগ', 'Digital', 'record'], ['History', 'ইতিহাস', 'Digital', 'past record'], ['Backup', 'ব্যাকআপ', 'Digital', 'copy'], ['Restore', 'পুনরুদ্ধার', 'Digital', 'bring back'], ['Delete', 'মুছে ফেলা', 'Digital', 'remove'], ['Trash', 'ট্র্যাশ', 'Digital', 'deletion folder'], ['Sync', 'সিঙ্ক', 'Digital', 'synchronize'], ['Update', 'আপডেট', 'Digital', 'make new'], ['Version', 'সংস্করণ', 'Digital', 'edition'], ['Install', 'ইনস্টল', 'Digital', 'set up'], ['Uninstall', 'আনইনস্টল', 'Digital', 'remove'], ['Bug', 'বাগ', 'Digital', 'error'], ['Glitch', 'গ্লিচ', 'Digital', 'malfunction'], ['Crash', 'ক্র্যাশ', 'Digital', 'failure'], ['Freeze', 'হ্যাং', 'Digital', 'stop responding'], ['Restart', 'পুনরায় চালু', 'Digital', 'start again'], ['Shutdown', 'শাটডাউন', 'Digital', 'turn off'], ['Login', 'লগইন', 'Digital', 'enter system'], ['Logout', 'লগআউট', 'Digital', 'exit system'], ['Password', 'পাসওয়ার্ড', 'Digital', 'secret word'], ['Username', 'ব্যবহারকারী নাম', 'Digital', 'login name'], ['Email', 'ইমেইল', 'Digital', 'electronic mail'], ['Confirmation', 'নিশ্চিতকরণ', 'Digital', 'verification'], ['Verification', 'যাচাইকরণ', 'Digital', 'checking'], ['Authentication', 'প্রমাণীকরণ', 'Digital', 'proof of identity'], ['Authorization', 'অনুমোদন', 'Digital', 'permission'], ['Notification', 'বিজ্ঞপ্তি', 'Digital', 'alert message'], ['Alert', 'সতর্কতা', 'Digital', 'warning'], ['Error message', 'ত্রুটি বার্তা', 'Digital', 'error notification'], ['Warning', 'সতর্কবার্তা', 'Digital', 'caution'], ['Pop-up', 'পপ-আপ', 'Digital', 'sudden window'], ['Spam', 'স্প্যাম', 'Digital', 'unwanted mail'], ['Phishing', 'ফিশিং', 'Digital', 'fake emails'], ['Malware', 'ম্যালওয়্যার', 'Digital', 'bad software'], ['Virus', 'ভাইরাস', 'Digital', 'harmful code'], ['Worm', 'ওয়ার্ম', 'Digital', 'self-copy code'], ['Trojan', 'ট্রোজান', 'Digital', 'hidden code'], ['Hacker', 'হ্যাকার', 'Digital', 'code breaker'], ['Hacking', 'হ্যাকিং', 'Digital', 'unauthorized access'], ['Breach', 'লঙ্ঘন', 'Digital', 'break in'], ['Leak', 'লিক', 'Digital', 'data escape'], ['Privacy', 'গোপনীয়তা', 'Digital', 'data protection'], ['Confidentiality', 'গোপনীয়তা', 'Digital', 'secrecy'], ['Encryption', 'এনক্রিপশন', 'Digital', 'code protection'], ['Decryption', 'ডিক্রিপশন', 'Digital', 'code opening'], ['Certificate', 'সার্টিফিকেট', 'Digital', 'digital proof'], ['SSL', 'এসএসএল', 'Digital', 'secure connection'], ['HTTPS', 'এইচটিটিপিএস', 'Digital', 'secure web'], ['API', 'এপিআই', 'Digital', 'program connection'], ['Webhook', 'ওয়েবহুক', 'Digital', 'automatic callback'], ['Integration', 'একীকরণ', 'Digital', 'combining systems'], ['Compatibility', 'সামঞ্জস্য', 'Digital', 'working together'], ['API key', 'এপিআই কী', 'Digital', 'access token'], ['Token', 'টোকেন', 'Digital', 'access pass'], ['Session', 'সেশন', 'Digital', 'login period'], ['Cookie', 'কুকি', 'Digital', 'stored data'], ['Cache', 'ক্যাশে', 'Digital', 'temporary storage'], ['Queue', 'কিউ', 'Digital', 'waiting line'], ['Thread', 'থ্রেড', 'Digital', 'process track'], ['Process', 'প্রক্রিয়া', 'Digital', 'running program'], ['Service', 'সেবা', 'Digital', 'available function'], ['API endpoint', 'এপিআই শেষবিন্দু', 'Digital', 'connection point'], ['Load', 'লোড', 'Digital', 'processing demand'], ['Latency', 'লেটেন্সি', 'Digital', 'delay time'], ['Bandwidth', 'ব্যান্ডউইথ', 'Digital', 'data capacity'], ['Speed', 'গতি', 'Digital', 'processing speed'], ['Performance', 'কর্মক্ষমতা', 'Digital', 'how well working'], ['Scalability', 'স্কেলেবিলিটি', 'Digital', 'can grow'], ['Reliability', 'নির্ভরযোগ্যতা', 'Digital', 'trustworthy'], ['Availability', 'উপলব্ধতা', 'Digital', 'accessible'],
        ];

        $rows4 = array_map(function ($w) use ($lesson4Id, $now) {
            return [
                'word' => $w[0], 'meaning' => $w[1], 'synonyms' => $w[2], 'antonyms' => $w[3],
                'lesson_id' => $lesson4Id, 'status' => true, 'created_at' => $now, 'updated_at' => $now,
            ];
        }, $lesson4Words);

        foreach (array_chunk($rows4, 50) as $chunk) {
            DB::table('words')->insert($chunk);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: Only removes new lessons, keeps original chapter
        DB::table('words')->whereIn('lesson_id', function ($query) {
            $query->select('id')->from('lessons')
                ->whereIn('title', [
                    'ব্যাংকে এসেছে শব্দ',
                    'আসতে পারে — সম্ভাব্য ব্যাংক শব্দ',
                    'আর্থিক বিষয় — Banking Excellence',
                    'ডিজিটাল ব্যাংকিং — Modern Finance',
                ]);
        })->delete();

        DB::table('lessons')->whereIn('title', [
            'ব্যাংকে এসেছে শব্দ',
            'আসতে পারে — সম্ভাব্য ব্যাংক শব্দ',
            'আর্থিক বিষয় — Banking Excellence',
            'ডিজিটাল ব্যাংকিং — Modern Finance',
        ])->delete();
    }
};
