<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Verb section clean-up found in the 2026-10-14 content audit
 * (chapter 2 "Essential Verbs for All Learners", lessons 2 and 5).
 *
 *  - wrong or unrelated verb forms (Pay = Played, Shut = Shouted, Lend = Landed,
 *    Fling = Flew, Cap = Caped, Cost = Costed, Outrun = Outturned, ...),
 *  - entries whose English word itself was wrong (Asceses, Carcss, Tit,
 *    Behaved, Searched, Elected, Addicted),
 *  - wrong / misspelt Bangla meanings (Find = দেখা, Like = লাইক দেয়া,
 *    Dare = গ্রাহ্য করা, Inaugurate = উদ্ভাবন, Irk = পরিত্যাক্ত, ...),
 *  - lesson 5 no longer claims to be an "IELTS" list.
 *
 * Only words.word / meaning / synonyms (= Verb 2) / antonyms (= Verb 3) and
 * the title of lesson 5 change. Every change is applied ONLY when the cell
 * still holds the exact value that was audited (otherwise it is skipped and
 * logged), the old values go to verb_content_fix_backup for down(),
 * everything runs in one transaction and the migration never throws (cannot
 * block boot).
 */
return new class extends Migration
{
    private const BACKUP = 'verb_content_fix_backup';

    private function changes(): array
    {
        return json_decode(<<<'JSON'
{"words":[[978,"meaning","মনোযোগ দেওয়া","উপস্থিত থাকা, মনোযোগ দেওয়া"],[988,"meaning","খাপ খাওয়ানো","জায়গা দেওয়া, মানিয়ে নেওয়া"],[992,"meaning","ব্যাথা বা বেদনা দেওয়া","ব্যথা করা"],[994,"meaning","পরিচিত হওয়া","পরিচিত করানো"],[996,"antonyms","Addict","Added"],[997,"word","Addicted","Addict"],[997,"meaning","নেশাগ্রস্ত হওয়া","আসক্ত হওয়া, আসক্ত করা"],[998,"meaning","শাসন করা","পরিচালনা করা, প্রশাসন করা"],[1012,"meaning","কৌতুক করা","আনন্দ দেওয়া, মজা দেওয়া"],[1015,"meaning","হাসা","ক্ষমা চাওয়া"],[1016,"meaning","হাসা","আবেদন করা, আপিল করা"],[1037,"meaning","জাগরিত করা","জাগা, জাগানো"],[1038,"meaning","পুরষ্কারে ভূষিত করা","পুরস্কারে ভূষিত করা"],[1039,"word","Asceses","Assess"],[1039,"synonyms","Abscessed","Assessed"],[1039,"antonyms","Abscessed","Assessed"],[1051,"word","Behaved","Believe"],[1051,"meaning","ভদ্রভাবে আচরণ করা","বিশ্বাস করা"],[1061,"meaning","ভাঙ্গা","ভাঙা"],[1068,"meaning","প্যান প্যান করা","বারবার জ্বালাতন করা"],[1069,"meaning","জামিনে খালাশ করা","জামিনে খালাস করা"],[1073,"meaning","জারজ ঘোষণা করা","বিকৃত করা, মান নষ্ট করা"],[1077,"meaning","উদগিরণ হওয়া","ঢেকুর তোলা"],[1078,"meaning","সত্বাধীন হওয়া","অধিকারভুক্ত হওয়া, সম্পর্কিত হওয়া"],[1080,"meaning","বাধা","বাঁধা"],[1089,"meaning","একঘেয়েমি করা","বিরক্ত করা, একঘেয়ে করে তোলা"],[1094,"antonyms","Showed","Shown"],[1096,"meaning","দাঁড়ান","দাঁড়ানো"],[1099,"meaning","ঝাঁপ দাও","লাফানো, ঝাঁপ দেওয়া"],[1100,"meaning","লাইক দেয়া","পছন্দ করা"],[1102,"meaning","হাঁসা","মুচকি হাসা"],[1105,"meaning","শুনুন","শোনা"],[1108,"meaning","ঝাকি দেয়া","ঝাঁকানো"],[1121,"meaning","গ্রাহ্য করা","সাহস করা"],[1123,"meaning","ব্যবহার করা, কারবার করা","মোকাবিলা করা, লেনদেন করা, বণ্টন করা"],[1127,"meaning","দেখা","খুঁজে পাওয়া"],[1130,"synonyms","Flew","Flung"],[1130,"antonyms","Flown","Flung"],[1139,"meaning","আছে","আছে, থাকা"],[1145,"meaning","দেওয়া","অনুমতি দেওয়া, দেওয়া"],[1146,"meaning","চালানো","নেতৃত্ব দেওয়া, পরিচালনা করা"],[1154,"meaning","উৎপাদন করা","প্রজনন করা, পালন করা"],[1156,"synonyms","Broadcasted","Broadcast"],[1156,"antonyms","Broadcasted","Broadcast"],[1158,"meaning","ঝাড়ু দেওয়া","ব্রাশ দিয়ে পরিষ্কার করা"],[1163,"meaning","ফাটিয়া পরা","ফেটে পড়া"],[1168,"meaning","টুপি","টুপি পরানো"],[1168,"synonyms","Caped","Capped"],[1168,"antonyms","Caped","Capped"],[1170,"meaning","নগদ প্রদান করা","নগদে ভাঙানো"],[1184,"synonyms",null,"Colored"],[1184,"antonyms",null,"Colored"],[1185,"meaning","চিরনি দিয়ে আচড়ানো","চিরুনি দিয়ে আঁচড়ানো"],[1192,"meaning","পূর্ন মনোযোগ দেওয়া","পূর্ণ মনোযোগ দেওয়া"],[1194,"meaning","নিশ্চিত হওয়া","নিশ্চিত করা"],[1201,"meaning","টেলিফোনে যোগাযোগ করা","যোগাযোগ করা"],[1202,"meaning","ধারন করা","ধারণ করা"],[1205,"meaning","দান করা","অবদান রাখা, চাঁদা দেওয়া"],[1212,"meaning","দাম করা","মূল্য হওয়া, খরচ পড়া"],[1212,"synonyms","Costed","Cost"],[1212,"antonyms","Costed","Cost"],[1219,"meaning","কোকড়ানো","কোঁকড়ানো"],[1227,"meaning","ভোট দেওয়া","ভোট চাওয়া, প্রচার চালানো"],[1230,"word","Carcss","Caress"],[1233,"meaning","সমালোচনা করা","নিন্দা করা"],[1244,"meaning","কেটে যাওয়া","চিরে ফেলা"],[1257,"meaning","অভিনন্দন জানানো","প্রশংসা করা"],[1258,"meaning","মত দেওয়া","মেনে চলা"],[1264,"meaning","শোক প্রকাশ করা","সমবেদনা জানানো"],[1266,"meaning","বিশ্বাস করে গোপন করা","বিশ্বাস করে গোপন কথা বলা"],[1274,"meaning","গভীরভাবে চিড় করা","গভীরভাবে চিন্তা করা"],[1275,"meaning","তর্ক করা","প্রতিদ্বন্দ্বিতা করা, লড়াই করা"],[1275,"synonyms","Contested`","Contested"],[1278,"meaning","আদর/প্রশয় দেওয়া","আদর/প্রশ্রয় দেওয়া"],[1287,"meaning","বুকে হাঁটিয়া চলা","হামাগুড়ি দিয়ে চলা"],[1288,"meaning","ব্যাথা হওয়া","ব্যথা হওয়া"],[1343,"meaning","চাঁদা দেওয়া","দান করা"],[1355,"meaning","গভীরতর","গভীর করা"],[1362,"meaning","হতাশ করা","হতাশ হওয়া"],[1365,"meaning","ভিন্ন মত পোষন করা","ভিন্ন মত পোষণ করা"],[1368,"meaning","হতাশা / আশাভঙ্গ","হতাশ করা"],[1369,"meaning","থেমে যাওয়া","বন্ধ করা"],[1390,"synonyms","Drug","Drugged"],[1390,"antonyms","Drug","Drugged"],[1392,"meaning","হুবহুব নকল করা","হুবহু নকল করা"],[1413,"word","Elected","Elongate"],[1414,"meaning","প্রেমিকের সাথে পরামর্শ করা","প্রেমিকের সাথে পালিয়ে যাওয়া"],[1425,"meaning","যৌতুক দেওয়া","দান করা, গুণসম্পন্ন করা"],[1430,"meaning","সবসময় কাজে লেগে থাকা","পুরোপুরি মগ্ন করা"],[1439,"meaning","চাপিয়ে দেওয়া","আবশ্যিকভাবে জড়িত করা, বয়ে আনা"],[1444,"meaning","বিশ্বাস স্থাপন করা","দায়িত্ব দেওয়া, বিশ্বাস করে জিম্মা দেওয়া"],[1444,"antonyms",null,"Entrusted"],[1451,"meaning","বাতিল করা","বাদ দেওয়া"],[1462,"meaning","মরে যাওয়া","মেয়াদ শেষ হওয়া"],[1472,"meaning","ব্যর্থ হওয়","ব্যর্থ হওয়া"],[1483,"word","Tit","Fit"],[1494,"meaning","জমিয়ে যাওয়া","জমে যাওয়া"],[1502,"meaning","মোট করা","মোটা করা"],[1506,"meaning","গর্জিয়ে তোলা/উত্তেজিত করা","গাঁজানো, উত্তেজিত করা"],[1507,"meaning","পচানো","পেকে ওঠা, পচে যাওয়া"],[1520,"meaning","প্রবাহিত করা","প্রবাহিত হওয়া"],[1529,"meaning","হতাশ হওয়া","হতাশ করা"],[1529,"antonyms","Furnished","Frustrated"],[1534,"meaning","গল্প করা","পরচর্চা করা"],[1535,"meaning","উদ্বোধন করা, শুরু করা","মুখ বন্ধ করা, মুখে কাপড় গুঁজে দেওয়া"],[1538,"meaning","হাই তোলা","হাঁ করে তাকিয়ে থাকা"],[1544,"antonyms","Gladden","Gladdened"],[1552,"meaning","দুঃখ দেওয়া","শোক করা, দুঃখ দেওয়া"],[1582,"synonyms","Harbord","Harbored"],[1582,"antonyms","Harbord","Harbored"],[1598,"synonyms","The Humbugged","Humbugged"],[1609,"meaning","প্রশিক্ষণ দেওয়া","নির্দেশ দেওয়া"],[1611,"meaning","সাক্ষাৎকার করা","সাক্ষাৎকার নেওয়া"],[1623,"meaning","উদ্ভাবন করা","উদ্বোধন করা"],[1626,"meaning","একত্রে করা","অন্তর্ভুক্ত করা, একীভূত করা"],[1628,"synonyms","Influence","Influenced"],[1628,"antonyms","influence","Influenced"],[1629,"meaning","জানান","জানানো"],[1633,"meaning","অনসন্ধান করা","অনুসন্ধান করা"],[1641,"meaning","মধ্যে পথে বাঁধা দেওয়া","মাঝপথে বাধা দেওয়া"],[1647,"meaning","পরিত্যাক্ত করা","বিরক্ত করা"],[1650,"meaning","খোচা মারা","খোঁচা মারা"],[1657,"meaning","ছেড়ে দেওয়া","জ্যাক দিয়ে তোলা"],[1660,"meaning","চাকুরী","ঠিকা কাজ করা, দালালি করা"],[1673,"meaning","ধাপ্পা দেওয়া","ঠাট্টা করা, ধাপ্পা দেওয়া"],[1675,"meaning","বোনা বা বপন করা","বোনা, বুনন করা"],[1693,"meaning","কমানো","আলগা করা, ঢিলা করা"],[1697,"meaning","অভাব হওয়া","অভাব থাকা"],[1705,"meaning","ধার করা","ধার দেওয়া"],[1705,"synonyms","Landed","Lent"],[1705,"antonyms","Landed","Lent"],[1717,"meaning","শাসন করা","প্রভুত্ব করা"],[1731,"meaning","ভুল করা/ বুঝা","ভুল বোঝা"],[1742,"antonyms","Marked","Marketed"],[1749,"meaning","মুখস্ত করা","মুখস্থ করা"],[1751,"meaning","বিশৃঙ্খল রা","বিশৃঙ্খল করা"],[1752,"meaning","মনে করা","আপত্তি করা, খেয়াল রাখা"],[1794,"antonyms","Outdid","Outdone"],[1795,"synonyms","Outturned","Outran"],[1795,"antonyms","Outturned","Outrun"],[1806,"synonyms","Played","Paid"],[1806,"antonyms","Played","Paid"],[1810,"meaning","তুলে ফেলা","তোলা, বাছাই করা"],[1817,"meaning","পূর্থণা করা","প্রার্থনা করা"],[1833,"synonyms","Partake","Partook"],[1833,"antonyms","Partake","Partaken"],[1843,"meaning","আঙ্গুল নির্দেশ করা","আঙুল দিয়ে দেখানো"],[1858,"meaning","ছাপা দেওয়া","চাপ দেওয়া, চাপা"],[1861,"meaning","অবসর হওয়া","অগ্রসর হওয়া"],[1867,"meaning","প্রকল্প তৈরী করা","প্রক্ষেপণ করা, পরিকল্পনা করা"],[1868,"meaning","অভিযোগ করা","মামলা করা, বিচারের মুখোমুখি করা"],[1881,"meaning","লাইনে দাড়ানো","লাইনে দাঁড়ানো"],[1882,"antonyms","Quickened","Quietened"],[1884,"meaning","রাণী সুলভ অরণ করা","রানির মতো আচরণ করা"],[1889,"meaning","মূল্য ধার্য্য করা","মূল্য ধার্য করা"],[1890,"meaning","পৌছানো","পৌঁছানো"],[1899,"meaning","উপলব্ধি","উপলব্ধি করা"],[1908,"meaning","পুনরূদ্ধার করা","পুনরুদ্ধার করা"],[1910,"meaning","ব্যবস্থা করা/নির্দেশ/উল্লেখ","উল্লেখ করা, সূত্র নির্দেশ করা"],[1914,"meaning","ভক্তি করা","বিবেচনা করা, সম্মান করা"],[1919,"meaning","প্রত্যাখান/ বাতিল করা","প্রত্যাখ্যান করা, বাতিল করা"],[1923,"synonyms","Remarked","Remade"],[1923,"antonyms","Remarked","Remade"],[1926,"meaning","মিল/ সদৃশ্য হওয়া","মিল থাকা, সদৃশ হওয়া"],[1929,"meaning","সাঁড়া দেওয়া","সাড়া দেওয়া"],[1944,"meaning","চেঁচামেচি করে ফেলা","হই-হুল্লোড় করে খেলা"],[1952,"meaning","যাত্রা করা","শুরু করা"],[1960,"synonyms","Shouted","Shut"],[1960,"antonyms","Shouted","Shut"],[1972,"meaning","বঁকুনি/ গালাগালি করা","বকুনি দেওয়া, গালাগালি করা"],[1973,"meaning","তলাশি করা","তল্লাশি করা"],[1974,"word","Searched","See"]],"lessons":[[5,"title","Most Important Verbs for IELTS – ৩টি Form, বাংলা অর্থ ও উচ্চারণ","Verb List – ৩টি Form, বাংলা অর্থ ও উচ্চারণ"]]}
JSON, true);
    }

    public function up(): void
    {
        try {
            $this->apply();
        } catch (\Throwable $e) {
            Log::error('[verb-fix] aborted, nothing was changed: ' . $e->getMessage());
        }
    }

    private function apply(): void
    {
        foreach (['words', 'lessons'] as $t) {
            if (!Schema::hasTable($t)) { return; }
        }
        if (Schema::hasTable(self::BACKUP)) { Log::info('[verb-fix] already applied'); return; }

        $ch = $this->changes();
        $wordPlan   = [];   // [id, col, old, new]
        $lessonPlan = [];
        $skipped = 0;

        $ids = array_values(array_unique(array_map(fn ($c) => $c[0], $ch['words'])));
        $rows = [];
        foreach (array_chunk($ids, 500) as $chunk) {
            foreach (DB::table('words')->whereIn('id', $chunk)->get(['id', 'word', 'meaning', 'synonyms', 'antonyms']) as $r) {
                $rows[$r->id] = $r;
            }
        }
        foreach ($ch['words'] as [$id, $col, $old, $new]) {
            if (!isset($rows[$id]) || (string) $rows[$id]->{$col} !== (string) $old || ($rows[$id]->{$col} === null) !== ($old === null)) {
                $skipped++;
                continue;
            }
            $wordPlan[] = [$id, $col, $old, $new];
        }
        foreach ($ch['lessons'] as [$id, $col, $old, $new]) {
            $cur = DB::table('lessons')->where('id', $id)->value($col);
            if (($cur === null) !== ($old === null) || (string) $cur !== (string) $old) { $skipped++; continue; }
            $lessonPlan[] = [$id, $col, $old, $new];
        }

        if (!$wordPlan && !$lessonPlan) { Log::info('[verb-fix] nothing to change (skipped ' . $skipped . ')'); return; }

        Schema::create(self::BACKUP, function ($t) {
            $t->id();
            $t->string('kind', 10);
            $t->unsignedBigInteger('ref_id');
            $t->string('col', 20);
            $t->text('old_value')->nullable();
        });

        try {
            DB::transaction(function () use ($wordPlan, $lessonPlan) {
                $now = now();
                foreach ($wordPlan as [$id, $col, $old, $new]) {
                    DB::table(self::BACKUP)->insert(['kind' => 'word', 'ref_id' => $id, 'col' => $col, 'old_value' => $old]);
                    $q = DB::table('words')->where('id', $id);
                    $old === null ? $q->whereNull($col) : $q->where($col, $old);
                    if ($q->update([$col => $new, 'updated_at' => $now]) !== 1) {
                        throw new \RuntimeException("word {$id}.{$col} changed concurrently");
                    }
                }
                foreach ($lessonPlan as [$id, $col, $old, $new]) {
                    DB::table(self::BACKUP)->insert(['kind' => 'lesson', 'ref_id' => $id, 'col' => $col, 'old_value' => $old]);
                    $q = DB::table('lessons')->where('id', $id);
                    $old === null ? $q->whereNull($col) : $q->where($col, $old);
                    if ($q->update([$col => $new, 'updated_at' => $now]) !== 1) {
                        throw new \RuntimeException("lesson {$id}.{$col} changed concurrently");
                    }
                }
            });
        } catch (\Throwable $e) {
            Schema::dropIfExists(self::BACKUP);
            throw $e;
        }

        Log::info('[verb-fix] words changed: ' . count($wordPlan) . ', lesson labels changed: ' . count($lessonPlan) . ', skipped (value differs): ' . $skipped);
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::BACKUP)) { return; }
        DB::transaction(function () {
            foreach (DB::table(self::BACKUP)->get() as $r) {
                $table = $r->kind === 'word' ? 'words' : 'lessons';
                DB::table($table)->where('id', $r->ref_id)->update([$r->col => $r->old_value]);
            }
        });
        Schema::dropIfExists(self::BACKUP);
    }
};
