<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Bank chapter clean-up after 2026_10_12_000015:
 *
 *  1. "may come" lessons: the third column held the meaningless tags
 *     "Probable" / "Finance" / "Fintech". Each word gets a short English
 *     synonym instead and the column is labelled "English Synonym"; the
 *     importance % column is labelled "গুরুত্ব %" (it was mislabelled
 *     "English Synonym"). A lesson is converted only when EVERY word has a
 *     mapped synonym and still carries one of the three tags.
 *  2. "came" lessons: the numeric importance column gets the honest label
 *     "গুরুত্ব %" (only when all its values are numbers).
 *  3. 25 clearly wrong or weak Bangla meanings are corrected (for example
 *     Equity = "সামগ্রিক মূল্য", Compound = "যৌগিক", Journal = "পত্রিকা"), each
 *     only if the meaning still equals the known old value.
 *
 * Only words.synonyms / words.meaning and lessons.col3_label / col4_label
 * change. Old values go to bank_fix_backup for down(). Guarded, one
 * transaction + verification, logged, never throws (cannot block boot).
 */
return new class extends Migration
{
    private const BACKUP         = 'bank_fix_backup';
    private const CAME_TITLE     = '🏦 Bank English — পরীক্ষায় এসেছে';
    private const PROBABLE_TITLE = '🏦 Bank English — ভবিষ্যৎ পরীক্ষায় আসতে পারে';
    private const TAGS           = ['Probable', 'Finance', 'Fintech'];
    private const SYN_LABEL      = '🔤 English Synonym';
    private const IMP_LABEL      = '📊 গুরুত্ব %';

    private function synonyms(): array
    {
        return json_decode(<<<'JSON'
{
 "accounting": "recording of finances",
 "agreement": "mutual understanding",
 "allowance": "extra pay",
 "amortization": "gradual repayment",
 "analysis": "detailed examination",
 "api": "software interface",
 "app": "software application",
 "arbitrage": "profit from price differences",
 "assets": "valuable holdings",
 "audit": "official inspection",
 "authentication": "proof of identity",
 "bandwidth": "data capacity",
 "bankruptcy": "insolvency",
 "barcode": "striped scan code",
 "benefit": "advantage",
 "bill": "statement of charges",
 "biometric": "body-feature based",
 "bitcoin": "first crypto coin",
 "blockchain": "chain of linked records",
 "bond": "debt security",
 "bonus": "extra payment",
 "bookkeeping": "recording transactions",
 "boom": "rapid growth",
 "budget": "spending plan",
 "business": "commercial activity",
 "bust": "sudden collapse",
 "capital": "funds; wealth",
 "cash flow": "movement of money",
 "claim": "demand for payment",
 "cloud": "online storage",
 "commerce": "business trade",
 "compensation": "payment for loss",
 "compliance": "obeying rules",
 "connection": "link",
 "consumer": "end user",
 "contract": "binding agreement",
 "cost": "expense",
 "coverage": "extent of protection",
 "credit score": "creditworthiness rating",
 "cryptocurrency": "digital currency",
 "data": "information",
 "database": "stored data set",
 "debt": "borrowed money",
 "deed": "legal document",
 "deflation": "falling prices",
 "demand": "consumer need",
 "depreciation": "fall in value",
 "depression": "severe slump",
 "derivative": "contract based on an asset",
 "digital": "electronic",
 "discount": "price reduction",
 "distributed ledger": "shared record book",
 "dividend": "profit share",
 "economics": "study of wealth and production",
 "economy": "system of production and trade",
 "encryption": "coding for secrecy",
 "equity": "ownership interest",
 "expense": "expenditure",
 "face recognition": "face-based identification",
 "finance": "money management",
 "financial": "monetary",
 "fingerprint": "finger ridge pattern",
 "forecast": "prediction",
 "fraud": "deception",
 "futures": "forward contracts",
 "gateway": "entry point",
 "hardware": "physical equipment",
 "hedge": "protect against loss",
 "incentive": "reward",
 "income": "earnings",
 "industry": "manufacturing sector",
 "inflation": "rising prices",
 "insurance": "financial protection",
 "interface": "point of interaction",
 "internet": "global network",
 "investment": "capital outlay",
 "invoice": "bill for goods",
 "kyc": "customer identity check",
 "latency": "delay",
 "liabilities": "debts owed",
 "liquidity": "ease of converting to cash",
 "loss": "deficit",
 "market": "place of trade",
 "markup": "added profit amount",
 "mobile": "portable",
 "network": "connected system",
 "nfc": "short-range wireless",
 "npa": "non-performing asset",
 "obligation": "duty",
 "online": "connected to the internet",
 "option": "right to buy or sell",
 "otp": "one-time password",
 "password": "secret word",
 "payroll": "list of employee pay",
 "pin": "secret number",
 "platform": "base system",
 "portfolio": "collection of investments",
 "premium": "insurance payment",
 "price": "cost",
 "production": "manufacturing",
 "profit": "gain",
 "profit margin": "profit percentage",
 "promissory note": "written promise to pay",
 "qr code": "square scan code",
 "ratios": "proportions",
 "recession": "economic slump",
 "regulation": "official rule",
 "revenue": "income; turnover",
 "rfid": "radio tag technology",
 "risk": "possibility of loss",
 "salary": "fixed pay",
 "server": "network computer",
 "share": "unit of ownership",
 "smart contract": "self-executing agreement",
 "software": "computer programs",
 "solvency": "ability to pay debts",
 "speculation": "risky trading for gain",
 "speed": "rate",
 "stock": "company shares",
 "supervision": "oversight",
 "supplier": "provider of goods",
 "supply": "available quantity",
 "swap": "exchange of obligations",
 "tax": "levy",
 "technology": "applied science",
 "token": "digital unit",
 "trade": "buying and selling",
 "value": "worth",
 "vendor": "seller",
 "verification": "confirmation",
 "wage": "payment for labour",
 "wallet": "e-money store",
 "warrant": "right to buy shares"
}
JSON, true);
    }

    private function fixes(): array
    {
        return json_decode(<<<'JSON'
{
 "amortization": [
  "পরিশোধ",
  "ঋণ কিস্তিতে পরিশোধ / অবলোপন"
 ],
 "balance": [
  "ভারসাম্য",
  "জের / হিসাবের অবশিষ্ট অর্থ"
 ],
 "compliance": [
  "সম্মতি",
  "বিধি-প্রতিপালন"
 ],
 "compound": [
  "যৌগিক",
  "চক্রবৃদ্ধি (সুদ)"
 ],
 "coverage": [
  "কভারেজ",
  "বীমার আওতা"
 ],
 "credit": [
  "জমা",
  "ক্রেডিট (হিসাবে অর্থ জমা)"
 ],
 "debit": [
  "উত্তোলন",
  "ডেবিট (হিসাব থেকে অর্থ কাটা)"
 ],
 "default": [
  "ডিফল্ট",
  "ঋণখেলাপি / পরিশোধে ব্যর্থতা"
 ],
 "equity": [
  "সামগ্রিক মূল্য",
  "মালিকানা-স্বত্ব / ইক্যুইটি"
 ],
 "face recognition": [
  "মুখ চেনা",
  "মুখাবয়ব শনাক্তকরণ"
 ],
 "journal": [
  "পত্রিকা",
  "জাবেদা খাতা"
 ],
 "kyc": [
  "কাস্টমার শনাক্ত",
  "গ্রাহক পরিচয় যাচাই"
 ],
 "ledger": [
  "খাতা",
  "খতিয়ান"
 ],
 "liquidity": [
  "তরলতা",
  "তারল্য"
 ],
 "markup": [
  "মূল্য বৃদ্ধি",
  "ক্রয়মূল্যের উপর যুক্ত মুনাফা"
 ],
 "maturity": [
  "পরিপক্বতা",
  "মেয়াদপূর্তি"
 ],
 "npa": [
  "নন-পারফর্মিং",
  "অনুৎপাদনশীল সম্পদ (খেলাপি ঋণ)"
 ],
 "overdraft": [
  "ওভারড্রাফট",
  "জমার অতিরিক্ত উত্তোলন"
 ],
 "premium": [
  "প্রিমিয়াম",
  "বীমার কিস্তি (প্রিমিয়াম)"
 ],
 "principal": [
  "মূলধন",
  "আসল (ঋণের মূল অঙ্ক)"
 ],
 "regulation": [
  "নিয়ম",
  "বিধিমালা / নিয়ন্ত্রণ"
 ],
 "solvency": [
  "সামর্থ্য",
  "ঋণ পরিশোধের সামর্থ্য"
 ],
 "speculation": [
  "অনুমান",
  "ফাটকা কারবার"
 ],
 "statement": [
  "বিবৃতি",
  "হিসাব-বিবরণী"
 ],
 "value": [
  "মূল্যবান",
  "মূল্য"
 ]
}
JSON, true);
    }

    public function up(): void
    {
        try {
            $this->apply();
        } catch (\Throwable $e) {
            Log::error('[bank-fix] aborted, nothing was changed: ' . $e->getMessage());
        }
    }

    private function apply(): void
    {
        foreach (['chapters', 'lessons', 'words'] as $t) {
            if (!Schema::hasTable($t)) { return; }
        }
        if (!Schema::hasColumn('lessons', 'col3_label') || !Schema::hasColumn('lessons', 'col4_label')) { return; }
        if (Schema::hasTable(self::BACKUP)) { Log::info('[bank-fix] already applied'); return; }

        $came = DB::table('chapters')->where('title', self::CAME_TITLE)->orderBy('id')->first();
        $may  = DB::table('chapters')->where('title', self::PROBABLE_TITLE)->orderBy('id')->first();
        if (!$came || !$may) { Log::warning('[bank-fix] bank chapters not found, nothing changed'); return; }

        $syn = $this->synonyms();
        $fix = $this->fixes();

        $wordUpdates   = [];   // id => ['synonyms' => [old,new]] / ['meaning' => [old,new]]
        $lessonUpdates = [];   // id => [old col3, old col4, new col3|null, new col4]

        // 1. may-come lessons -> English synonyms + importance label
        foreach (DB::table('lessons')->where('chapter_id', $may->id)->orderBy('id')->get() as $l) {
            $words = DB::table('words')->where('lesson_id', $l->id)->orderBy('id')->get(['id', 'word', 'synonyms', 'antonyms', 'meaning']);
            if ($words->isEmpty()) { continue; }
            $ok = true;
            $plan = [];
            foreach ($words as $w) {
                $key = mb_strtolower(trim((string) $w->word), 'UTF-8');
                if (!in_array(trim((string) $w->synonyms), self::TAGS, true) || !isset($syn[$key])) { $ok = false; break; }
                $plan[$w->id] = ['synonyms' => [(string) $w->synonyms, $syn[$key]]];
            }
            if (!$ok) { continue; }
            foreach ($plan as $id => $p) { $wordUpdates[$id] = $p; }
            $lessonUpdates[$l->id] = [$l->col3_label, $l->col4_label, self::SYN_LABEL, self::IMP_LABEL];
        }

        // 2. came lessons: label the numeric column honestly
        foreach (DB::table('lessons')->where('chapter_id', $came->id)->orderBy('id')->get() as $l) {
            $values = DB::table('words')->where('lesson_id', $l->id)->pluck('antonyms')->all();
            if (!$values) { continue; }
            $numeric = true;
            foreach ($values as $v) { if (!ctype_digit(trim((string) $v))) { $numeric = false; break; } }
            if ($numeric && $l->col4_label !== self::IMP_LABEL) {
                $lessonUpdates[$l->id] = [$l->col3_label, $l->col4_label, null, self::IMP_LABEL];
            }
        }

        // 3. meaning fixes (both chapters)
        $lessonIds = DB::table('lessons')->whereIn('chapter_id', [$came->id, $may->id])->pluck('id')->all();
        foreach (DB::table('words')->whereIn('lesson_id', $lessonIds)->get(['id', 'word', 'meaning']) as $w) {
            $key = mb_strtolower(trim((string) $w->word), 'UTF-8');
            if (isset($fix[$key]) && (string) $w->meaning === $fix[$key][0]) {
                $wordUpdates[$w->id]['meaning'] = [(string) $w->meaning, $fix[$key][1]];
            }
        }

        if (!$wordUpdates && !$lessonUpdates) { Log::info('[bank-fix] nothing to change'); return; }

        Schema::create(self::BACKUP, function ($t) {
            $t->id();
            $t->string('kind', 10);            // word | lesson
            $t->unsignedBigInteger('ref_id');
            $t->string('col', 20)->nullable();
            $t->text('old_value')->nullable();
        });

        try {
            DB::transaction(function () use ($wordUpdates, $lessonUpdates) {
                $now = now();
                foreach ($wordUpdates as $id => $cols) {
                    foreach ($cols as $col => [$old, $new]) {
                        DB::table(self::BACKUP)->insert(['kind' => 'word', 'ref_id' => $id, 'col' => $col, 'old_value' => $old]);
                        $n = DB::table('words')->where('id', $id)->where($col, $old)->update([$col => $new, 'updated_at' => $now]);
                        if ($n !== 1) { throw new \RuntimeException("word {$id}.{$col} changed concurrently"); }
                    }
                }
                foreach ($lessonUpdates as $id => [$old3, $old4, $new3, $new4]) {
                    DB::table(self::BACKUP)->insert(['kind' => 'lesson', 'ref_id' => $id, 'col' => 'col3_label', 'old_value' => $old3]);
                    DB::table(self::BACKUP)->insert(['kind' => 'lesson', 'ref_id' => $id, 'col' => 'col4_label', 'old_value' => $old4]);
                    $upd = ['col4_label' => $new4, 'updated_at' => $now];
                    if ($new3 !== null) { $upd['col3_label'] = $new3; }
                    DB::table('lessons')->where('id', $id)->update($upd);
                }
                // verification: no tag left in the converted lessons
                $converted = array_keys(array_filter($lessonUpdates, fn ($u) => $u[2] !== null));
                if ($converted) {
                    $left = DB::table('words')->whereIn('lesson_id', $converted)->whereIn('synonyms', self::TAGS)->count();
                    if ($left !== 0) { throw new \RuntimeException("{$left} tags left after conversion"); }
                }
            });
        } catch (\Throwable $e) {
            Schema::dropIfExists(self::BACKUP);
            throw $e;
        }

        Log::info('[bank-fix] words changed: ' . count($wordUpdates) . ', lesson labels changed: ' . count($lessonUpdates));
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::BACKUP)) {
            return;
        }
        DB::transaction(function () {
            foreach (DB::table(self::BACKUP)->get() as $r) {
                $table = $r->kind === 'word' ? 'words' : 'lessons';
                DB::table($table)->where('id', $r->ref_id)->update([$r->col => $r->old_value]);
            }
        });
        Schema::dropIfExists(self::BACKUP);
    }
};
