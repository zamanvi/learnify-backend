<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Wizard ("ইতিহাসের অদ্ভুত পাতা") story clean-up found in the 2026-10-14
 * content audit.
 *
 *  - facts: Centralia sinkhole boy was 1981 (not 1980); Emu War tally was
 *    just under a thousand emus for nearly ten thousand bullets; the
 *    Tanganyika epidemic affected about a thousand people and closed some
 *    schools, not "a country's schools";
 *  - wrong vocabulary meanings (hysteria = "stress", frost = "snowfall",
 *    scarce = "shortage", funding = "money (approval)");
 *  - misspelt / awkward Bangla (টাঙ্গানিয়িকা, খোঁজ-পার্টি, a sentence that
 *    said the smell "solved" the crisis) and two grammar notes that
 *    mis-described the structure.
 *
 * Every edit is guarded: a field value must equal the audited old value, a
 * text fragment must occur exactly once in the paragraph / note, a
 * vocabulary entry must still hold the audited value. Anything that no longer
 * matches (e.g. an admin edited the story meanwhile) is skipped and logged.
 * Old column values go to wizard_story_fix_backup and are restored by down().
 * One transaction; the migration never throws (cannot block boot).
 */
return new class extends Migration
{
    private const BACKUP = 'wizard_story_fix_backup';
    private const JSON_COLS = ['english_paragraphs', 'bangla_paragraphs', 'grammar_notes', 'vocabulary'];

    private function data(): array
    {
        return json_decode(<<<'JSON'
{"2":{"para":[["bangla_paragraphs",1,"খোঁজ-পার্টিগুলো থেকে","তল্লাশি দলগুলোর হাত থেকে"]],"vocab":[["leaflets","meaning","লিফলেট","প্রচারপত্র, লিফলেট"],["officer","meaning","অফিসার","কর্মকর্তা, অফিসার"],["commanding","meaning","কমান্ডিং","নেতৃত্বদানকারী, কমান্ডিং"]]},"3":{"para":[["english_paragraphs",2,"only a few hundred emus had been killed, out of thousands of bullets fired","fewer than a thousand emus had been killed, despite nearly ten thousand bullets fired"],["bangla_paragraphs",2,"হাজার হাজার গুলি ছোড়া হলেও মাত্র কয়েকশ এমু মারা পড়েছিল","প্রায় দশ হাজার গুলি ছোড়া হলেও এক হাজারেরও কম এমু মারা পড়েছিল"]]},"4":{"scalar":[["hook_title","The Laughter That Shut Down a Country's Schools","The Laughter That Shut Down Schools"],["bangla_title","যে হাসি একটা দেশের স্কুল বন্ধ করে দিয়েছিল","যে হাসি একের পর এক স্কুল বন্ধ করে দিয়েছিল"]],"para":[["bangla_paragraphs",0,"টাঙ্গানিয়িকা","টাঙ্গানিকা"],["english_paragraphs",2,"well over a thousand people were affected","around a thousand people were affected"],["bangla_paragraphs",2,"হাজারেরও বেশি মানুষ এতে আক্রান্ত হয়েছিল","প্রায় এক হাজার মানুষ এতে আক্রান্ত হয়েছিল"],["bangla_paragraphs",3,"\"mass hysteria\"","\"mass hysteria\" (গণ-হিস্টিরিয়া)"]],"vocab":[["hysteria","meaning","মানসিক চাপ (হিস্টিরিয়া)","গণ-হিস্টিরিয়া, উন্মত্ততা"],["hysteria","phonetic","হিস্টেরিয়া","হিস্টিরিয়া"]]},"5":{"para":[["bangla_paragraphs",3,"জন্য অর্থ অনুমোদন করল","জন্য অর্থ বরাদ্দ অনুমোদন করল"],["bangla_paragraphs",3,"লন্ডনের সংকট সমাধান করেছিল কোনো ওষুধ বা প্রতিবাদ না — সমাধান করেছিল সেই দুর্গন্ধ,","লন্ডনের নর্দমা-সংকটের সমাধান এনেছিল ওষুধ বা প্রতিবাদ নয় — বরং সেই দুর্গন্ধ,"]],"vocab":[["funding","meaning","অর্থ (অনুমোদন)","তহবিল, অর্থ বরাদ্দ"]]},"7":{"para":[["english_paragraphs",2,"In 1980, a boy","In 1981, a boy"],["bangla_paragraphs",2,"১৯৮০ সালে, একটা ছেলে","১৯৮১ সালে, একটা ছেলে"]]},"8":{"notes":[[1,"বোঝায় এই ঘটনাটা আরেকটা past ঘটনার আগেই ঘটার কথা ছিল কিন্তু ঘটেইনি। \"even\" জোর দিচ্ছে বিস্ময় বোঝাতে।","দেখায় যে গন্তব্যে পৌঁছানোর কাজটা তখনও শেষ হয়নি — তার আগেই অন্য ঘটনা (ধাক্কা) ঘটে গেছে। \"even\" বিস্ময় ও জোর যোগ করেছে।"]]},"9":{"para":[["bangla_paragraphs",1,"ইন্দোনেশিয়ার Tambora আগ্নেয়গিরি","ইন্দোনেশিয়ার তাম্বোরা (Tambora) আগ্নেয়গিরি"],["bangla_paragraphs",2,"তুষারপাতে ফসল নষ্ট হলো। খাবারের অভাব দেখা দিল,","হিমের কারণে ফসল নষ্ট হলো। খাবার দুষ্প্রাপ্য হয়ে উঠল,"]],"vocab":[["frost","meaning","তুষারপাত","হিম, তুষার"],["scarce","meaning","অভাব","অপ্রতুল, দুষ্প্রাপ্য"]]},"10":{"notes":[[1,"বাংলায় \"worse\"-কে ক্রিয়াবিশেষণ বানিয়ে \"অবস্থা আরও খারাপ হলো\" আকারে অনুবাদ হয়।","এখানে worse একটি adjective; বাংলায় \"অবস্থা আরও খারাপ হলো\" আকারে অনুবাদ হয়।"]]}}
JSON, true);
    }

    public function up(): void
    {
        try {
            $this->apply();
        } catch (\Throwable $e) {
            Log::error('[wizard-fix] aborted, nothing was changed: ' . $e->getMessage());
        }
    }

    private function enc($v): string
    {
        return json_encode($v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function apply(): void
    {
        if (!Schema::hasTable('wizard_stories')) { return; }
        if (Schema::hasTable(self::BACKUP)) { Log::info('[wizard-fix] already applied'); return; }

        $skipped = 0;
        $plan = []; // story id => [col => [old_raw, new_raw]]

        foreach ($this->data() as $id => $ops) {
            $row = DB::table('wizard_stories')->where('id', (int) $id)->first();
            if (!$row) { $skipped += $this->countOps($ops); continue; }

            $cur = [];   // working copy: col => string | array
            foreach (['hook_title', 'bangla_title'] as $c) { $cur[$c] = $row->$c; }
            foreach (self::JSON_COLS as $c) {
                $cur[$c] = $row->$c === null ? null : json_decode($row->$c, true);
            }
            $touched = [];

            foreach ($ops['scalar'] ?? [] as [$col, $old, $new]) {
                if (($cur[$col] ?? null) === $old) { $cur[$col] = $new; $touched[$col] = true; } else { $skipped++; }
            }
            foreach ($ops['para'] ?? [] as [$col, $idx, $old, $new]) {
                $p = $cur[$col][$idx] ?? null;
                if (is_string($p) && substr_count($p, $old) === 1) {
                    $cur[$col][$idx] = str_replace($old, $new, $p);
                    $touched[$col] = true;
                } else { $skipped++; }
            }
            foreach ($ops['notes'] ?? [] as [$idx, $old, $new]) {
                $t = $cur['grammar_notes'][$idx]['text'] ?? null;
                if (is_string($t) && substr_count($t, $old) === 1) {
                    $cur['grammar_notes'][$idx]['text'] = str_replace($old, $new, $t);
                    $touched['grammar_notes'] = true;
                } else { $skipped++; }
            }
            foreach ($ops['vocab'] ?? [] as [$word, $field, $old, $new]) {
                $hit = null;
                foreach ($cur['vocabulary'] ?? [] as $i => $v) {
                    if (($v['word'] ?? null) === $word && ($v[$field] ?? null) === $old) { $hit = $i; break; }
                }
                if ($hit === null) { $skipped++; continue; }
                $cur['vocabulary'][$hit][$field] = $new;
                $touched['vocabulary'] = true;
            }

            foreach (array_keys($touched) as $c) {
                $oldRaw = $row->$c;
                $newRaw = in_array($c, self::JSON_COLS, true) ? $this->enc($cur[$c]) : $cur[$c];
                if ($oldRaw !== $newRaw) { $plan[(int) $id]['cols'][$c] = [$oldRaw, $newRaw]; }
            }
            if (isset($plan[(int) $id])) { $plan[(int) $id]['stamp'] = $row->updated_at; }
        }

        if (!$plan) { Log::info('[wizard-fix] nothing to change (skipped ' . $skipped . ')'); return; }

        Schema::create(self::BACKUP, function ($t) {
            $t->id();
            $t->unsignedBigInteger('story_id');
            $t->string('col', 30);
            $t->longText('old_value')->nullable();
        });

        $done = 0;
        $cells = 0;
        try {
            DB::transaction(function () use ($plan, &$skipped, &$done, &$cells) {
                $now = now();
                foreach ($plan as $id => $p) {
                    $set = [];
                    foreach ($p['cols'] as $col => [$old, $new]) { $set[$col] = $new; }
                    $q = DB::table('wizard_stories')->where('id', $id);
                    $p['stamp'] === null ? $q->whereNull('updated_at') : $q->where('updated_at', $p['stamp']);
                    // one statement per story; skipped when an admin saved it in the meantime
                    if ($q->update($set + ['updated_at' => $now]) !== 1) { $skipped += count($set); continue; }
                    foreach ($p['cols'] as $col => [$old, $new]) {
                        DB::table(self::BACKUP)->insert(['story_id' => $id, 'col' => $col, 'old_value' => $old]);
                    }
                    $done++;
                    $cells += count($set);
                }
            });
        } catch (\Throwable $e) {
            Schema::dropIfExists(self::BACKUP);
            throw $e;
        }

        Log::info('[wizard-fix] stories changed: ' . $done . ', columns changed: ' . $cells . ', skipped (no longer matches): ' . $skipped);
    }

    private function countOps(array $ops): int
    {
        return array_sum(array_map('count', $ops));
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::BACKUP)) { return; }
        DB::transaction(function () {
            foreach (DB::table(self::BACKUP)->get() as $r) {
                DB::table('wizard_stories')->where('id', $r->story_id)->update([$r->col => $r->old_value]);
            }
        });
        Schema::dropIfExists(self::BACKUP);
    }
};
