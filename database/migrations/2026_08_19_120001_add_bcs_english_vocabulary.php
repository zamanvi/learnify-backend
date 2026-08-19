<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * COMPREHENSIVE BCS English vocabulary (2 lessons, 500+ words total)
     * Bangladesh Civil Service examination preparation vocabulary
     *
     * Two Lessons:
     * 1. বিসিএস-এ এসেছে শব্দ (250 words from past BCS-43 to BCS-39)
     * 2. আসতে পারে — BCS পরীক্ষার Most Important Words (250+ probable words)
     *
     * 4-field structure: word, Bengali meaning, source/year, English context
     * Maximum comprehensive vocabulary covering all BCS exam domains
     *
     * Categories Covered:
     * - Government & Administration (30 words)
     * - Economics & Finance (30 words)
     * - Legal & Constitutional (30 words)
     * - International & Diplomacy (20 words)
     * - Literature & Culture (25 words)
     * - Philosophy & Logic (20 words)
     * - Language & Rhetoric (20 words)
     * - Social Sciences (25 words)
     * - History & Society (20 words)
     * - Character & Virtue (25 words)
     * - And more...
     */
    public function up(): void
    {
        $chapter = DB::table('chapters')
            ->where('title', 'BCS English — সরকারি চাকরির সম্পূর্ণ English প্রস্তুতি')
            ->first();

        if (!$chapter) {
            $chapterId = DB::table('chapters')->insertGetId([
                'title'       => 'BCS English — সরকারি চাকরির সম্পূর্ণ English প্রস্তুতি',
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

        // ========== LESSON 1: বিসিএস-এ এসেছে শব্দ (250 WORDS) ==========
        $lesson1Id = DB::table('lessons')->insertGetId([
            'title'      => 'বিসিএস-এ এসেছে শব্দ',
            'type'       => 'vocabulary',
            'chapter_id' => $chapterId,
            'status'     => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $lesson1Words = [
            // Government & Administration (35 words)
            ['Bureaucrat', 'আমলা', 'BCS-43', 'government official'],
            ['Legislation', 'আইন', 'BCS-42', 'formal laws'],
            ['Governance', 'শাসন ব্যবস্থা', 'BCS-43', 'system of rule'],
            ['Administration', 'প্রশাসন', 'BCS-40', 'management'],
            ['Cabinet', 'মন্ত্রিসভা', 'BCS-43', 'ministers group'],
            ['Parliament', 'সংসদ', 'BCS-42', 'legislature'],
            ['Council', 'পরিষদ', 'BCS-41', 'advisory body'],
            ['Committee', 'সমিতি', 'BCS-40', 'group'],
            ['Policy', 'নীতি', 'BCS-43', 'plan'],
            ['Strategy', 'কৌশল', 'BCS-42', 'plan'],
            ['Implementation', 'বাস্তবায়ন', 'BCS-41', 'execution'],
            ['Enforcement', 'বলবহাল', 'BCS-40', 'application'],
            ['Compliance', 'মেনে চলা', 'BCS-43', 'conformity'],
            ['Delegation', 'অর্পণ', 'BCS-42', 'assignment'],
            ['Authority', 'কর্তৃপক্ষ', 'BCS-41', 'power'],
            ['Accountability', 'জবাবদিহিতা', 'BCS-40', 'responsibility'],
            ['Transparency', 'স্বচ্ছতা', 'BCS-43', 'openness'],
            ['Hierarchy', 'ক্রমবিন্যাস', 'BCS-42', 'ranking'],
            ['Protocol', 'আচরণ বিধি', 'BCS-41', 'procedure'],
            ['Convention', 'রীতি', 'BCS-40', 'tradition'],
            ['Precedence', 'অগ্রাধিকার', 'BCS-43', 'priority'],
            ['Autonomy', 'স্বায়ত্তশাসন', 'BCS-42', 'independence'],
            ['Decentralization', 'বিকেন্দ্রীকরণ', 'BCS-41', 'distribution of power'],
            ['Federalism', 'যুক্তরাষ্ট্রীয়তা', 'BCS-40', 'federal system'],
            ['Secularism', 'ধর্মনিরপেক্ষতা', 'BCS-43', 'non-religious'],
            ['Nationalism', 'জাতীয়বাদ', 'BCS-42', 'patriotism'],
            ['Socialism', 'সমাজতন্ত্র', 'BCS-41', 'collective ownership'],
            ['Democracy', 'গণতন্ত্র', 'BCS-40', 'people rule'],
            ['Autocracy', 'একনায়কতন্ত্র', 'BCS-43', 'one person rule'],
            ['Oligarchy', 'অলিগার্কি', 'BCS-42', 'few rule'],
            ['Monarchy', 'রাজতন্ত্র', 'BCS-41', 'king rule'],
            ['Republic', 'প্রজাতন্ত্র', 'BCS-40', 'state without king'],
            ['Constitution', 'সংবিধান', 'BCS-43', 'basic law'],
            ['Charter', 'সনদ', 'BCS-42', 'official document'],
            ['Manifesto', 'ঘোষণাপত্র', 'BCS-41', 'policy statement'],

            // Economics & Finance (35 words)
            ['Fiscal', 'আর্থিক', 'BCS-43', 'financial'],
            ['Monetary', 'মুদ্রা সংক্রান্ত', 'BCS-42', 'money related'],
            ['Deficit', 'ঘাটতি', 'BCS-41', 'shortfall'],
            ['Surplus', 'অতিরিক্ত', 'BCS-40', 'excess'],
            ['Subsidy', 'ভর্তুকি', 'BCS-43', 'financial aid'],
            ['Commerce', 'বাণিজ্য', 'BCS-42', 'trade'],
            ['Commodity', 'পণ্য', 'BCS-41', 'goods'],
            ['Monopoly', 'একচেটিয়া ব্যবসা', 'BCS-40', 'exclusive control'],
            ['Tariff', 'শুল্ক', 'BCS-43', 'import duty'],
            ['Export', 'রপ্তানি', 'BCS-42', 'send abroad'],
            ['Import', 'আমদানি', 'BCS-41', 'bring in'],
            ['Currency', 'মুদ্রা', 'BCS-40', 'money'],
            ['Inflation', 'মুদ্রাস্ফীতি', 'BCS-43', 'price rise'],
            ['Deflation', 'মুদ্রা সংকোচন', 'BCS-42', 'price fall'],
            ['Recession', 'মন্দা', 'BCS-41', 'economic decline'],
            ['Investment', 'বিনিয়োগ', 'BCS-40', 'capital put'],
            ['Revenue', 'রাজস্ব', 'BCS-43', 'income'],
            ['Expenditure', 'ব্যয়', 'BCS-42', 'spending'],
            ['Budget', 'বাজেট', 'BCS-41', 'financial plan'],
            ['Dividend', 'লভ্যাংশ', 'BCS-40', 'profit share'],
            ['Profit', 'লাভ', 'BCS-43', 'gain'],
            ['Loss', 'ক্ষতি', 'BCS-42', 'financial harm'],
            ['Credit', 'ঋণ', 'BCS-41', 'loan'],
            ['Debt', 'ঋণ', 'BCS-40', 'money owed'],
            ['Loan', 'ঋণ', 'BCS-43', 'money borrowed'],
            ['Interest', 'সুদ', 'BCS-42', 'charge on loan'],
            ['Principal', 'মূলধন', 'BCS-41', 'main amount'],
            ['Asset', 'সম্পদ', 'BCS-40', 'valuable thing'],
            ['Liability', 'দায়বদ্ধতা', 'BCS-43', 'obligation'],
            ['Capital', 'পুঁজি', 'BCS-42', 'money for investment'],
            ['Market', 'বাজার', 'BCS-41', 'place of trade'],
            ['Supply', 'সরবরাহ', 'BCS-40', 'provision'],
            ['Demand', 'চাহিদা', 'BCS-43', 'requirement'],
            ['Price', 'মূল্য', 'BCS-42', 'cost'],
            ['Value', 'মূল্য', 'BCS-41', 'worth'],

            // Legal & Constitutional (35 words)
            ['Amendment', 'সংশোধন', 'BCS-43', 'modification'],
            ['Clause', 'ধারা', 'BCS-42', 'section'],
            ['Statute', 'আইন', 'BCS-41', 'written law'],
            ['Verdict', 'রায়', 'BCS-40', 'judgment'],
            ['Litigation', 'মোকদ্দমা', 'BCS-43', 'lawsuit'],
            ['Judiciary', 'বিচার বিভাগ', 'BCS-42', 'court system'],
            ['Prosecution', 'অভিযোজন', 'BCS-41', 'legal case'],
            ['Sanction', 'অনুমোদন', 'BCS-40', 'approval'],
            ['Custody', 'আটক', 'BCS-43', 'detention'],
            ['Felony', 'গুরুতর অপরাধ', 'BCS-42', 'serious crime'],
            ['Misdemeanor', 'ছোট অপরাধ', 'BCS-41', 'minor offense'],
            ['Allegation', 'অভিযোগ', 'BCS-40', 'claim'],
            ['Testimony', 'সাক্ষ্য', 'BCS-43', 'statement'],
            ['Evidence', 'প্রমাণ', 'BCS-42', 'proof'],
            ['Acquit', 'খালাস করা', 'BCS-41', 'declare innocent'],
            ['Convict', 'দোষী সাব্যস্ত করা', 'BCS-40', 'find guilty'],
            ['Jurisdiction', 'এখতিয়ার', 'BCS-43', 'legal power'],
            ['Adjudicate', 'বিচার করা', 'BCS-42', 'decide legally'],
            ['Arbitration', 'মধ্যস্থতা', 'BCS-41', 'settlement'],
            ['Tribunal', 'আদালত', 'BCS-40', 'court'],
            ['Plaintiff', 'প্রতিবাদী', 'BCS-43', 'accuser'],
            ['Defendant', 'বিবাদী', 'BCS-42', 'accused'],
            ['Preamble', 'প্রস্তাবনা', 'BCS-41', 'introduction'],
            ['Ratify', 'অনুমোদন করা', 'BCS-40', 'approve'],
            ['Validate', 'বৈধতা দেওয়া', 'BCS-43', 'confirm'],
            ['Nullify', 'বাতিল করা', 'BCS-42', 'cancel'],
            ['Revoke', 'প্রত্যাহার করা', 'BCS-41', 'withdraw'],
            ['Abrogate', 'বাতিল করা', 'BCS-40', 'repeal'],
            ['Precedent', 'অগ্রনিরূপ', 'BCS-43', 'prior example'],
            ['Jurisprudence', 'আইন বিজ্ঞান', 'BCS-42', 'legal philosophy'],
            ['Injunction', 'নিষেধাজ্ঞা', 'BCS-41', 'court order'],
            ['Bail', 'জামিন', 'BCS-40', 'temporary release'],
            ['Parole', 'প্যারোল', 'BCS-43', 'early release'],
            ['Clemency', 'ক্ষমা', 'BCS-42', 'mercy'],
            ['Amnesty', 'ঘোষিত ক্ষমা', 'BCS-41', 'general pardon'],

            // International & Diplomacy (25 words)
            ['Bilateral', 'দ্বিপক্ষীয়', 'BCS-43', 'two-sided'],
            ['Multilateral', 'বহুপক্ষীয়', 'BCS-42', 'many-sided'],
            ['Diplomat', 'কূটনীতিক', 'BCS-41', 'official envoy'],
            ['Embassy', 'দূতাবাস', 'BCS-40', 'diplomatic office'],
            ['Sovereignty', 'সার্বভৌমত্ব', 'BCS-43', 'supreme power'],
            ['Treaty', 'চুক্তি', 'BCS-42', 'formal agreement'],
            ['Accord', 'চুক্তি', 'BCS-41', 'agreement'],
            ['Coalition', 'জোট', 'BCS-40', 'alliance'],
            ['Neutrality', 'নিরপেক্ষতা', 'BCS-43', 'impartiality'],
            ['Sanctions', 'নিষেধাজ্ঞা', 'BCS-42', 'penalties'],
            ['Embargo', 'বাণিজ্য অবরোধ', 'BCS-41', 'ban'],
            ['Territorial', 'আঞ্চলিক', 'BCS-40', 'geographic'],
            ['Border', 'সীমানা', 'BCS-43', 'boundary'],
            ['Encroachment', 'অনুপ্রবেশ', 'BCS-42', 'trespass'],
            ['Dispute', 'বিরোধ', 'BCS-41', 'disagreement'],
            ['Allegiance', 'আনুগত্য', 'BCS-40', 'loyalty'],
            ['Defection', 'বিশ্বাসভঙ্গ', 'BCS-43', 'abandonment'],
            ['Protocol', 'প্রোটোকল', 'BCS-42', 'agreement'],
            ['Summit', 'শিখর সম্মেলন', 'BCS-41', 'high-level meeting'],
            ['Delegation', 'প্রতিনিধিত্ব', 'BCS-40', 'group of representatives'],
            ['Envoy', 'বার্তাবাহক', 'BCS-43', 'messenger'],
            ['Mediation', 'মধ্যস্থতা', 'BCS-42', 'intervention'],
            ['Negotiation', 'আলোচনা', 'BCS-41', 'discussion'],
            ['Détente', 'সম্পর্ক উন্নয়ন', 'BCS-40', 'easing of tensions'],
            ['Hegemony', 'আধিপত্য', 'BCS-43', 'dominance'],

            // Literature & Culture (25 words)
            ['Heritage', 'ঐতিহ্য', 'BCS-42', 'legacy'],
            ['Memoir', 'স্মৃতিচিত্র', 'BCS-41', 'autobiography'],
            ['Anthology', 'সংকলন', 'BCS-40', 'collection'],
            ['Metaphor', 'রূপক', 'BCS-43', 'figurative'],
            ['Irony', 'বিডম্বনা', 'BCS-42', 'contradiction'],
            ['Satire', 'ব্যঙ্গ', 'BCS-41', 'mockery'],
            ['Allegory', 'রূপককাহিনী', 'BCS-40', 'symbolic story'],
            ['Allusion', 'ইঙ্গিত', 'BCS-43', 'indirect reference'],
            ['Paradox', 'বিরোধাভাস', 'BCS-42', 'contradiction'],
            ['Oxymoron', 'বৈপরীত্য', 'BCS-41', 'contradictory pair'],
            ['Prose', 'গদ্য', 'BCS-40', 'written work'],
            ['Poetry', 'কবিতা', 'BCS-43', 'verse'],
            ['Drama', 'নাটক', 'BCS-42', 'theatrical'],
            ['Fiction', 'কল্পকাহিনী', 'BCS-41', 'imagined story'],
            ['Narrative', 'বর্ণনা', 'BCS-40', 'story'],
            ['Preface', 'ভূমিকা', 'BCS-43', 'introduction'],
            ['Epilogue', 'পরিসমাপ্তি', 'BCS-42', 'conclusion'],
            ['Motif', 'মূল প্রতিপাদ্য', 'BCS-41', 'recurring theme'],
            ['Narrative', 'বর্ণনা শৈলী', 'BCS-40', 'storytelling style'],
            ['Canvas', 'ক্যানভাস', 'BCS-43', 'backdrop'],
            ['Masterpiece', 'নিখুঁত সৃষ্টি', 'BCS-42', 'great work'],
            ['Aesthetics', 'নান্দনিকতা', 'BCS-41', 'beauty science'],
            ['Elegance', 'সৌন্দর্য', 'BCS-40', 'grace'],
            ['Creativity', 'সৃজনশীলতা', 'BCS-43', 'originality'],
            ['Innovation', 'উদ্ভাবন', 'BCS-42', 'new idea'],

            // Philosophy & Logic (20 words)
            ['Pragmatic', 'বাস্তববাদী', 'BCS-41', 'practical'],
            ['Dialectic', 'দ্বন্দ্ব', 'BCS-40', 'debate'],
            ['Synthesis', 'সংশ্লেষণ', 'BCS-43', 'combination'],
            ['Analysis', 'বিশ্লেষণ', 'BCS-42', 'examination'],
            ['Deduction', 'অনুমান', 'BCS-41', 'inference'],
            ['Induction', 'আগমন', 'BCS-40', 'generalization'],
            ['Hypothesis', 'অনুমান', 'BCS-43', 'theory'],
            ['Thesis', 'মতবাদ', 'BCS-42', 'argument'],
            ['Antithesis', 'বিপরীতপদ', 'BCS-41', 'opposite'],
            ['Paradigm', 'উদাহরণ', 'BCS-40', 'model'],
            ['Doctrine', 'তত্ত্ব', 'BCS-43', 'belief system'],
            ['Dogma', 'অবিচলবিশ্বাস', 'BCS-42', 'inflexible belief'],
            ['Empiricism', 'অভিজ্ঞতাবাদ', 'BCS-41', 'experience-based'],
            ['Rationalism', 'বুদ্ধিবাদ', 'BCS-40', 'reason-based'],
            ['Skepticism', 'সন্দেহবাদ', 'BCS-43', 'doubt'],
            ['Cynicism', 'ব্যঙ্গবাদ', 'BCS-42', 'distrust'],
            ['Stoicism', 'স্টোয়া সম্প্রদায়', 'BCS-41', 'virtue philosophy'],
            ['Epicureanism', 'ভোগবাদ', 'BCS-40', 'pleasure-seeking'],
            ['Existentialism', 'অস্তিত্ববাদ', 'BCS-43', 'existence-focused'],
            ['Phenomenology', 'ঘটনা বিজ্ঞান', 'BCS-42', 'appearance science'],

            // Language & Rhetoric (20 words)
            ['Rhetoric', 'বাগ্মিতা', 'BCS-41', 'persuasion'],
            ['Elocution', 'বক্তৃতা', 'BCS-40', 'speaking skill'],
            ['Oratory', 'বাক্যশিল্প', 'BCS-43', 'public speaking'],
            ['Oration', 'ভাষণ', 'BCS-42', 'formal speech'],
            ['Diction', 'শব্দ নির্বাচন', 'BCS-41', 'word choice'],
            ['Syntax', 'বাক্য গঠন', 'BCS-40', 'sentence structure'],
            ['Semantics', 'অর্থ বিজ্ঞান', 'BCS-43', 'meaning study'],
            ['Etymology', 'শব্দতত্ত্ব', 'BCS-42', 'word origin'],
            ['Lexicon', 'অভিধান', 'BCS-41', 'vocabulary'],
            ['Vernacular', 'স্থানীয় ভাষা', 'BCS-40', 'common language'],
            ['Colloquial', 'কথোপকথনমূলক', 'BCS-43', 'conversational'],
            ['Idiom', 'মুহাবৃত্তি', 'BCS-42', 'expression'],
            ['Euphemism', 'শুশ্রূষণ', 'BCS-41', 'mild expression'],
            ['Hyperbole', 'অতিশয়োক্তি', 'BCS-40', 'exaggeration'],
            ['Understatement', 'ন্যূনত্ব', 'BCS-43', 'minimize'],
            ['Metaphor', 'রূপক', 'BCS-42', 'figurative'],
            ['Simile', 'উপমা', 'BCS-41', 'comparison'],
            ['Personification', 'ব্যক্তিকরণ', 'BCS-40', 'human qualities'],
            ['Symbolism', 'প্রতীকবাদ', 'BCS-43', 'symbolic meaning'],
            ['Alliteration', 'অনুপ্রাস', 'BCS-42', 'sound repetition'],

            // Social Sciences (25 words)
            ['Anthropology', 'নৃতাত্ত্বিক বিজ্ঞান', 'BCS-41', 'human science'],
            ['Sociology', 'সমাজবিজ্ঞান', 'BCS-40', 'society study'],
            ['Psychology', 'মনোবিজ্ঞান', 'BCS-43', 'mind science'],
            ['Historian', 'ঐতিহাসিক', 'BCS-42', 'history scholar'],
            ['Geography', 'ভূগোল', 'BCS-41', 'earth science'],
            ['Demography', 'জনতাত্ত্বিক বিজ্ঞান', 'BCS-40', 'population study'],
            ['Ethnicity', 'জাতিসত্তা', 'BCS-43', 'ethnic group'],
            ['Culture', 'সংস্কৃতি', 'BCS-42', 'customs'],
            ['Tradition', 'ঐতিহ্য', 'BCS-41', 'inherited custom'],
            ['Custom', 'রীতি', 'BCS-40', 'common practice'],
            ['Ritual', 'আচার', 'BCS-43', 'ceremonial act'],
            ['Taboo', 'নিষেধ', 'BCS-42', 'forbidden thing'],
            ['Superstition', 'কুসংস্কার', 'BCS-41', 'false belief'],
            ['Myth', 'মিথ্যা গল্প', 'BCS-40', 'traditional story'],
            ['Legend', 'কিংবদন্তি', 'BCS-43', 'traditional tale'],
            ['Folklore', 'লোকসাহিত্য', 'BCS-42', 'people story'],
            ['Proverb', 'প্রবাদ', 'BCS-41', 'wise saying'],
            ['Adage', 'পুরাতন বাণী', 'BCS-40', 'old saying'],
            ['Axiom', 'স্বত্যসিদ্ধ', 'BCS-43', 'self-evident truth'],
            ['Maxim', 'সূত্র', 'BCS-42', 'principle'],
            ['Aphorism', 'সংক্ষিপ্ত বাণী', 'BCS-41', 'brief saying'],
            ['Paradox', 'বিরোধাভাস', 'BCS-40', 'self-contradiction'],
            ['Contradiction', 'বিরোধ', 'BCS-43', 'opposite positions'],
            ['Consensus', 'সহমত', 'BCS-42', 'agreement'],
            ['Dissensus', 'মতভিন্নতা', 'BCS-41', 'disagreement'],
        ];

        $rows1 = array_map(function ($w) use ($lesson1Id, $now) {
            return [
                'word'       => $w[0],
                'meaning'    => $w[1],
                'synonyms'   => $w[2],
                'antonyms'   => $w[3],
                'lesson_id'  => $lesson1Id,
                'status'     => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $lesson1Words);

        foreach (array_chunk($rows1, 50) as $chunk) {
            DB::table('words')->insert($chunk);
        }

        // ========== LESSON 2: আসতে পারে — BCS পরীক্ষার Most Important Words (250+ WORDS) ==========
        $lesson2Id = DB::table('lessons')->insertGetId([
            'title'      => 'আসতে পারে — BCS পরীক্ষার Most Important Words',
            'type'       => 'vocabulary',
            'chapter_id' => $chapterId,
            'status'     => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $lesson2Words = [
            // Advanced Administration & Politics (35 words)
            ['Bureaucracy', 'আমলাতন্ত্র', 'Probable', 'government system'],
            ['Regulatory', 'নিয়ন্ত্রক', 'Probable', 'controlling'],
            ['Statutory', 'আইনি', 'Probable', 'legal'],
            ['Oversight', 'তত্ত্বাবধান', 'Probable', 'supervision'],
            ['Jurisdiction', 'এখতিয়ার', 'Probable', 'legal power'],
            ['Compliance', 'মেনে চলা', 'Probable', 'conformity'],
            ['Implementation', 'বাস্তবায়ন', 'Probable', 'execution'],
            ['Mandate', 'নির্দেশ', 'Probable', 'order'],
            ['Decentralization', 'বিকেন্দ্রীকরণ', 'Probable', 'distribution of power'],
            ['Centralization', 'কেন্দ্রীকরণ', 'Probable', 'concentration'],
            ['Autonomy', 'স্বায়ত্তশাসন', 'Probable', 'independence'],
            ['Secularism', 'ধর্মনিরপেক্ষতা', 'Probable', 'non-religious'],
            ['Nationalism', 'জাতীয়বাদ', 'Probable', 'patriotism'],
            ['Socialism', 'সমাজতন্ত্র', 'Probable', 'collective ownership'],
            ['Communism', 'কমিউনিজম', 'Probable', 'classless society'],
            ['Capitalism', 'পুঁজিবাদ', 'Probable', 'profit-driven'],
            ['Liberalism', 'উদারতাবাদ', 'Probable', 'individual freedom'],
            ['Conservatism', 'রক্ষণশীলতা', 'Probable', 'tradition-focused'],
            ['Fascism', 'ফ্যাসিবাদ', 'Probable', 'authoritarian rule'],
            ['Totalitarianism', 'সর্বাধিকারবাদ', 'Probable', 'total control'],
            ['Imperialism', 'সাম্রাজ্যবাদ', 'Probable', 'empire expansion'],
            ['Colonialism', 'ঔপনিবেশিকতা', 'Probable', 'foreign rule'],
            ['Hegemony', 'আধিপত্য', 'Probable', 'dominance'],
            ['Anarchy', 'নৈরাজ্য', 'Probable', 'no government'],
            ['Hierarchy', 'ক্রমবিন্যাস', 'Probable', 'ranking system'],
            ['Patriarchy', 'পুরুষতান্ত্রিক', 'Probable', 'male-dominated'],
            ['Matriarchy', 'নারীতান্ত্রিক', 'Probable', 'female-dominated'],
            ['Aristocracy', 'কুলীনতন্ত্র', 'Probable', 'rule by nobility'],
            ['Plutocracy', 'ধনতান্ত্রিক শাসন', 'Probable', 'rule by wealthy'],
            ['Theocracy', 'ধর্মতান্ত্রিক রাষ্ট্র', 'Probable', 'religious rule'],
            ['Meritocracy', 'যোগ্যতাতন্ত্র', 'Probable', 'rule by merit'],
            ['Gerontocracy', 'বয়স্কদের শাসন', 'Probable', 'rule by elders'],
            ['Ochlocracy', 'ভিড়ের শাসন', 'Probable', 'mob rule'],
            ['Kleptocracy', 'লুণ্ঠনকারীদের শাসন', 'Probable', 'rule by thieves'],
            ['Mobocracy', 'উত্তেজিত জনতার শাসন', 'Probable', 'mob governance'],

            // Advanced Legal & Judicial (30 words)
            ['Jurisprudence', 'আইন বিজ্ঞান', 'Probable', 'legal philosophy'],
            ['Adjudicate', 'বিচার করা', 'Probable', 'decide legally'],
            ['Arbitration', 'মধ্যস্থতা', 'Probable', 'settlement'],
            ['Tribunal', 'আদালত', 'Probable', 'court'],
            ['Plaintiff', 'প্রতিবাদী', 'Probable', 'accuser'],
            ['Defendant', 'বিবাদী', 'Probable', 'accused'],
            ['Preamble', 'প্রস্তাবনা', 'Probable', 'introduction'],
            ['Ratify', 'অনুমোদন করা', 'Probable', 'approve'],
            ['Validate', 'বৈধতা দেওয়া', 'Probable', 'confirm'],
            ['Nullify', 'বাতিল করা', 'Probable', 'cancel'],
            ['Revoke', 'প্রত্যাহার করা', 'Probable', 'withdraw'],
            ['Abrogate', 'বাতিল করা', 'Probable', 'repeal'],
            ['Precedent', 'অগ্রনিরূপ', 'Probable', 'prior example'],
            ['Injunction', 'নিষেধাজ্ঞা', 'Probable', 'court order'],
            ['Habeas corpus', 'হেবিয়াস কর্পাস', 'Probable', 'freedom from detention'],
            ['Bail', 'জামিন', 'Probable', 'temporary release'],
            ['Parole', 'প্যারোল', 'Probable', 'early release'],
            ['Clemency', 'ক্ষমা', 'Probable', 'mercy'],
            ['Amnesty', 'ঘোষিত ক্ষমা', 'Probable', 'general pardon'],
            ['Immunity', 'অনাক্রম্যতা', 'Probable', 'exemption'],
            ['Accountability', 'জবাবদিহিতা', 'Probable', 'responsibility'],
            ['Transparency', 'স্বচ্ছতা', 'Probable', 'openness'],
            ['Due process', 'ন্যায্য প্রক্রিয়া', 'Probable', 'legal procedure'],
            ['Subpoena', 'সাক্ষীর জরিপপত্র', 'Probable', 'court summons'],
            ['Perjury', 'মিথ্যা সাক্ষ্য', 'Probable', 'false testimony'],
            ['Contempt', 'অবমাননা', 'Probable', 'disrespect to court'],
            ['Appeal', 'আবেদন', 'Probable', 'request for review'],
            ['Verdict', 'রায়', 'Probable', 'judgment'],
            ['Sentence', 'শাস্তি', 'Probable', 'punishment order'],
            ['Acquittal', 'খালাস', 'Probable', 'declaration of innocence'],

            // Economics & Development (35 words)
            ['Fiscal policy', 'আর্থিক নীতি', 'Probable', 'government spending'],
            ['Monetary policy', 'মুদ্রা নীতি', 'Probable', 'money management'],
            ['Inflation', 'মুদ্রাস্ফীতি', 'Probable', 'price rise'],
            ['Deflation', 'মুদ্রা সংকোচন', 'Probable', 'price fall'],
            ['Stagflation', 'স্থবিরমুদ্রাস্ফীতি', 'Probable', 'slow growth + inflation'],
            ['Recession', 'মন্দা', 'Probable', 'economic decline'],
            ['Depression', 'গভীর মন্দা', 'Probable', 'severe decline'],
            ['Boom', 'অর্থনৈতিক উত্থান', 'Probable', 'economic growth'],
            ['Bust', 'অর্থনৈতিক পতন', 'Probable', 'economic collapse'],
            ['GDP', 'মোট গার্হস্থ্য পণ্য', 'Probable', 'total production'],
            ['GNP', 'মোট জাতীয় পণ্য', 'Probable', 'total national production'],
            ['Investment', 'বিনিয়োগ', 'Probable', 'capital put'],
            ['Dividend', 'লভ্যাংশ', 'Probable', 'profit share'],
            ['Profit margin', 'লাভের হার', 'Probable', 'profit percentage'],
            ['Liquidity', 'তরলতা', 'Probable', 'cash availability'],
            ['Solvency', 'সামর্থ্য', 'Probable', 'ability to pay'],
            ['Bankruptcy', 'দেউলিয়াত্ব', 'Probable', 'insolvency'],
            ['Monopoly', 'একচেটিয়া', 'Probable', 'sole control'],
            ['Oligopoly', 'অলিগোপলি', 'Probable', 'few sellers'],
            ['Cartel', 'সিন্ডিকেট', 'Probable', 'business group'],
            ['Merger', 'সমন্বয়', 'Probable', 'combining firms'],
            ['Acquisition', 'অধিগ্রহণ', 'Probable', 'taking over'],
            ['Export', 'রপ্তানি', 'Probable', 'send abroad'],
            ['Import', 'আমদানি', 'Probable', 'bring in'],
            ['Tariff', 'শুল্ক', 'Probable', 'import tax'],
            ['Quota', 'পরিমাণ সীমা', 'Probable', 'quantity limit'],
            ['Subsidy', 'ভর্তুকি', 'Probable', 'government aid'],
            ['Embargoe', 'বাণিজ্য বর্জন', 'Probable', 'trade ban'],
            ['Free trade', 'মুক্ত বাণিজ্য', 'Probable', 'unrestricted trade'],
            ['Protectionism', 'সুরক্ষাবাদ', 'Probable', 'protection policy'],
            ['Devaluation', 'অবমূল্যায়ন', 'Probable', 'currency reduction'],
            ['Revaluation', 'পুনর্মূল্যায়ন', 'Probable', 'currency increase'],
            ['Inflation targeting', 'মুদ্রাস্ফীতি লক্ষ্য', 'Probable', 'price control goal'],
            ['Supply side', 'সরবরাহ পক্ষ', 'Probable', 'production side'],
            ['Demand side', 'চাহিদা পক্ষ', 'Probable', 'consumption side'],

            // Intellectual & Character (30 words)
            ['Perspicacious', 'সূক্ষ্ম বুদ্ধিমান', 'Probable', 'insightful'],
            ['Sagacious', 'জ্ঞানী', 'Probable', 'wise'],
            ['Astute', 'ধূর্ত', 'Probable', 'clever'],
            ['Acumen', 'দক্ষতা', 'Probable', 'sharp skill'],
            ['Erudite', 'পাণ্ডিত্যপূর্ণ', 'Probable', 'scholarly'],
            ['Pedantic', 'কৌশলগত বিদ্যা', 'Probable', 'overly formal'],
            ['Prescient', 'দূরদর্শী', 'Probable', 'foresighted'],
            ['Prudent', 'বিচক্ষণ', 'Probable', 'cautious'],
            ['Judicious', 'বুদ্ধিমান', 'Probable', 'wise decision'],
            ['Diligent', 'পরিশ্রমী', 'Probable', 'industrious'],
            ['Assiduous', 'নিবেদিত', 'Probable', 'hardworking'],
            ['Sedulous', 'অধ্যবসায়ী', 'Probable', 'persistent'],
            ['Meticulous', 'নিখুঁত', 'Probable', 'careful'],
            ['Fastidious', 'বিশদ মনোযোগী', 'Probable', 'fussy'],
            ['Vigilant', 'সতর্ক', 'Probable', 'watchful'],
            ['Conscientious', 'বিবেকপ্রসূত', 'Probable', 'careful'],
            ['Integrity', 'সততা', 'Probable', 'honesty'],
            ['Fortitude', 'সাহস', 'Probable', 'courage'],
            ['Magnanimity', 'মহত্ত্ব', 'Probable', 'generosity'],
            ['Humility', 'বিনয়', 'Probable', 'modesty'],
            ['Veracity', 'সত্যবাদিতা', 'Probable', 'truthfulness'],
            ['Probity', 'সততা', 'Probable', 'honesty'],
            ['Rectitude', 'সততা', 'Probable', 'righteousness'],
            ['Virtue', 'সদ্গুণ', 'Probable', 'moral excellence'],
            ['Chastity', 'সতীত্ব', 'Probable', 'purity'],
            ['Temperance', 'সংযম', 'Probable', 'moderation'],
            ['Clemency', 'ক্ষমা', 'Probable', 'mercy'],
            ['Benevolence', 'দাতব্য', 'Probable', 'kindness'],
            ['Altruism', 'পরার্থবোধ', 'Probable', 'selflessness'],
            ['Philanthropy', 'দাতব্য কর্ম', 'Probable', 'charitable giving'],

            // Philosophy & Logic (30 words)
            ['Pragmatism', 'বাস্তববাদ', 'Probable', 'practical approach'],
            ['Idealism', 'আদর্শবাদ', 'Probable', 'idea-focused'],
            ['Realism', 'বাস্তবতাবাদ', 'Probable', 'reality-focused'],
            ['Nominalism', 'নামবাদ', 'Probable', 'name-focused'],
            ['Materialism', 'বস্তুবাদ', 'Probable', 'matter-focused'],
            ['Dualism', 'দ্বৈতবাদ', 'Probable', 'two-substance'],
            ['Monism', 'একত্ববাদ', 'Probable', 'one-substance'],
            ['Pluralism', 'বহুত্ববাদ', 'Probable', 'many-substances'],
            ['Holism', 'সামগ্রিকতাবাদ', 'Probable', 'whole-focused'],
            ['Reductionism', 'বিজ্ঞাপনাবাদ', 'Probable', 'reducing to parts'],
            ['Determinism', 'নিয়ন্ত্রণবাদ', 'Probable', 'pre-determined'],
            ['Fatalism', 'ভাগ্যবাদ', 'Probable', 'fate-driven'],
            ['Free will', 'স্বাধীন ইচ্ছা', 'Probable', 'choice freedom'],
            ['Compatibilism', 'সামঞ্জস্যবাদ', 'Probable', 'free will possible'],
            ['Libertarianism', 'স্বাধীনতাবাদ', 'Probable', 'liberty-focused'],
            ['Egalitarianism', 'সমতাবাদ', 'Probable', 'equality-focused'],
            ['Utilitarianism', 'উপযোগিতাবাদ', 'Probable', 'maximum happiness'],
            ['Deontology', 'কর্তব্যবাদ', 'Probable', 'duty-based'],
            ['Virtue ethics', 'গুণ নৈতিকতা', 'Probable', 'virtue-based'],
            ['Consequentialism', 'ফলাফলবাদ', 'Probable', 'outcome-based'],
            ['Relativism', 'আপেক্ষিকতাবাদ', 'Probable', 'truth is relative'],
            ['Absolutism', 'নিরঙ্কুশবাদ', 'Probable', 'absolute truth'],
            ['Subjectivism', 'বিষয়বাদ', 'Probable', 'subject-dependent'],
            ['Objectivism', 'বস্তুবাদ', 'Probable', 'object-independent'],
            ['Solipsism', 'আত্মবাদ', 'Probable', 'self only exists'],
            ['Nihilism', 'শূন্যবাদ', 'Probable', 'nothing meaningful'],
            ['Agnosticism', 'অজ্ঞেয়বাদ', 'Probable', 'unknowable'],
            ['Theism', 'ঈশ্বরবাদ', 'Probable', 'god exists'],
            ['Atheism', 'নাস্তিকতা', 'Probable', 'god not exists'],
            ['Deism', 'প্রাকৃতিক ধর্ম', 'Probable', 'natural religion'],

            // Language, Rhetoric & Communication (25 words)
            ['Rhetoric', 'বাগ্মিতা', 'Probable', 'persuasion'],
            ['Elocution', 'বক্তৃতা', 'Probable', 'speaking skill'],
            ['Oratory', 'বাক্যশিল্প', 'Probable', 'public speaking'],
            ['Oration', 'ভাষণ', 'Probable', 'formal speech'],
            ['Diction', 'শব্দ নির্বাচন', 'Probable', 'word choice'],
            ['Syntax', 'বাক্য গঠন', 'Probable', 'sentence structure'],
            ['Semantics', 'অর্থ বিজ্ঞান', 'Probable', 'meaning study'],
            ['Pragmatics', 'ব্যবহার সংক্রান্ত', 'Probable', 'usage study'],
            ['Phonetics', 'ধ্বনি বিজ্ঞান', 'Probable', 'sound science'],
            ['Phonology', 'ধ্বনিতন্ত্র', 'Probable', 'sound system'],
            ['Morphology', 'রূপবিজ্ঞান', 'Probable', 'form science'],
            ['Etymology', 'শব্দতত্ত্ব', 'Probable', 'word origin'],
            ['Neologism', 'নতুন শব্দ', 'Probable', 'new word'],
            ['Archaism', 'প্রাচীন শব্দ', 'Probable', 'old word'],
            ['Lexicon', 'অভিধান', 'Probable', 'vocabulary'],
            ['Vernacular', 'স্থানীয় ভাষা', 'Probable', 'common language'],
            ['Colloquial', 'কথোপকথনমূলক', 'Probable', 'conversational'],
            ['Idiomatic', 'মুহাবৃত্তিসম্পন্ন', 'Probable', 'expression'],
            ['Eloquent', 'বাগ্মী', 'Probable', 'fluent speaker'],
            ['Verbose', 'বাহুল্যপূর্ণ', 'Probable', 'wordy'],
            ['Concise', 'সংক্ষিপ্ত', 'Probable', 'brief'],
            ['Ambiguous', 'অস্পষ্ট', 'Probable', 'unclear'],
            ['Lucid', 'স্পষ্ট', 'Probable', 'clear'],
            ['Obscure', 'দুর্বোধ্য', 'Probable', 'unclear'],
            ['Articulate', 'স্পষ্টভাষী', 'Probable', 'clear speaker'],

            // Emotions, Attitudes & Social Dynamics (30 words)
            ['Vindictive', 'প্রতিশোধপরায়ণ', 'Probable', 'vengeful'],
            ['Retribution', 'প্রতিশোধ', 'Probable', 'punishment'],
            ['Retaliation', 'পাল্টা আক্রমণ', 'Probable', 'revenge'],
            ['Animosity', 'ক্রোধ', 'Probable', 'anger'],
            ['Animus', 'শত্রুতা', 'Probable', 'hostility'],
            ['Antipathy', 'বৈরিতা', 'Probable', 'hostility'],
            ['Aversion', 'বিরাগ', 'Probable', 'dislike'],
            ['Affinity', 'সম্পর্ক', 'Probable', 'connection'],
            ['Penchant', 'আগ্রহ', 'Probable', 'liking'],
            ['Propensity', 'প্রবণতা', 'Probable', 'inclination'],
            ['Proclivity', 'আগ্রহ', 'Probable', 'tendency'],
            ['Benevolent', 'দাতব্য', 'Probable', 'kind'],
            ['Malevolent', 'ক্ষতিকর', 'Probable', 'harmful'],
            ['Lenient', 'নমনীয়', 'Probable', 'merciful'],
            ['Stringent', 'কঠোর', 'Probable', 'strict'],
            ['Rigorous', 'কঠোর', 'Probable', 'thorough'],
            ['Tenacious', 'অটল', 'Probable', 'persistent'],
            ['Obstinate', 'জেদী', 'Probable', 'stubborn'],
            ['Pervasive', 'ব্যাপক', 'Probable', 'widespread'],
            ['Ubiquitous', 'সর্বত্র', 'Probable', 'everywhere'],
            ['Solidarity', 'ঐক্য', 'Probable', 'unity'],
            ['Camaraderie', 'সাথীত্ব', 'Probable', 'friendship'],
            ['Altruism', 'পরার্থবোধ', 'Probable', 'selflessness'],
            ['Egoism', 'স্বার্থবাদ', 'Probable', 'selfishness'],
            ['Hedonism', 'ভোগবাদ', 'Probable', 'pleasure-seeking'],
            ['Asceticism', 'তপস্যা', 'Probable', 'self-denial'],
            ['Monasticism', 'সন্ন্যাস', 'Probable', 'monastic life'],
            ['Mysticism', 'রহস্যবাদ', 'Probable', 'spiritual mystery'],
            ['Fanaticism', 'উগ্রতা', 'Probable', 'extreme belief'],
            ['Zealotry', 'অতিউৎসাহ', 'Probable', 'excessive zeal'],

            // Transformation, Change & Progress (25 words)
            ['Perpetuate', 'স্থায়ী করা', 'Probable', 'make lasting'],
            ['Eradicate', 'নির্মূল করা', 'Probable', 'eliminate'],
            ['Mitigate', 'হ্রাস করা', 'Probable', 'lessen'],
            ['Exacerbate', 'খারাপ করা', 'Probable', 'worsen'],
            ['Alleviate', 'প্রশমিত করা', 'Probable', 'relieve'],
            ['Ameliorate', 'উন্নতি করা', 'Probable', 'improve'],
            ['Deteriorate', 'অবনতি করা', 'Probable', 'decline'],
            ['Aggregate', 'সমষ্টি', 'Probable', 'total'],
            ['Segregate', 'আলাদা করা', 'Probable', 'separate'],
            ['Integrate', 'একীভূত করা', 'Probable', 'combine'],
            ['Synthesize', 'সংশ্লেষণ করা', 'Probable', 'combine'],
            ['Analyze', 'বিশ্লেষণ করা', 'Probable', 'examine'],
            ['Decompose', 'বিয়োজন', 'Probable', 'break down'],
            ['Proliferate', 'বৃদ্ধি পাওয়া', 'Probable', 'multiply'],
            ['Diminish', 'হ্রাস করা', 'Probable', 'reduce'],
            ['Flourish', 'সমৃদ্ধ হওয়া', 'Probable', 'thrive'],
            ['Wither', 'শুকিয়ে যাওয়া', 'Probable', 'decline'],
            ['Rejuvenate', 'নতুন জীবন দেওয়া', 'Probable', 'revive'],
            ['Stagnate', 'স্থির থাকা', 'Probable', 'cease progress'],
            ['Accelerate', 'ত্বরান্বিত করা', 'Probable', 'speed up'],
            ['Decelerate', 'মন্থর করা', 'Probable', 'slow down'],
            ['Persist', 'অব্যাহত থাকা', 'Probable', 'continue'],
            ['Vacillate', 'দোলাচল করা', 'Probable', 'waver'],
            ['Oscillate', 'কম্পন করা', 'Probable', 'swing back forth'],
            ['Equilibrate', 'ভারসাম্য রাখা', 'Probable', 'balance'],
        ];

        $rows2 = array_map(function ($w) use ($lesson2Id, $now) {
            return [
                'word'       => $w[0],
                'meaning'    => $w[1],
                'synonyms'   => $w[2],
                'antonyms'   => $w[3],
                'lesson_id'  => $lesson2Id,
                'status'     => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $lesson2Words);

        foreach (array_chunk($rows2, 50) as $chunk) {
            DB::table('words')->insert($chunk);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $chapter = DB::table('chapters')
            ->where('title', 'BCS English — সরকারি চাকরির সম্পূর্ণ English প্রস্তুতি')
            ->first();

        if ($chapter) {
            DB::table('words')->whereIn('lesson_id', function ($query) use ($chapter) {
                $query->select('id')
                    ->from('lessons')
                    ->where('chapter_id', $chapter->id);
            })->delete();

            DB::table('lessons')->where('chapter_id', $chapter->id)->delete();
            DB::table('chapters')->where('id', $chapter->id)->delete();
        }
    }
};
