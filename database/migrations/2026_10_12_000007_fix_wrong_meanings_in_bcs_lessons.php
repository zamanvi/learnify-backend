<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Eight BCS words carry a Bangla meaning that is simply wrong (for example
 * Plaintiff = "প্রতিবাদী", which is the opposite party; Subpoena = "সাক্ষীর
 * জরিপপত্র"; Objectivism = "বস্তুবাদ", the same as Materialism).
 *
 * A row is corrected ONLY when all three hold:
 *   - it sits in a lesson of a chapter titled "BCS English ..."
 *   - its word text matches (case-insensitive)
 *   - its meaning is still EXACTLY the known wrong value
 * so a meaning an editor has already fixed or changed is never overwritten, and
 * the same word in other chapters (e.g. Pedantic in the exam-words chapter) is
 * not touched.
 *
 * Only words.meaning changes. Old values go to bcs_meaning_fix_backup for down().
 * Guarded, one transaction + verification, logged, never throws (cannot block boot).
 */
return new class extends Migration
{
    private const BACKUP = 'bcs_meaning_fix_backup';

    /** word (lower-case) => [wrong meaning, correct meaning] */
    private const FIXES = [
        'plaintiff'    => ['প্রতিবাদী',          'বাদী'],
        'subpoena'     => ['সাক্ষীর জরিপপত্র',   'সমন'],
        'precedent'    => ['অগ্রনিরূপ',          'নজির'],
        'pedantic'     => ['কৌশলগত বিদ্যা',     'পাণ্ডিত্যাভিমানী'],
        'merger'       => ['সমন্বয়',            'একীভূতকরণ'],
        'reductionism' => ['বিজ্ঞাপনাবাদ',       'হ্রাসবাদ'],
        'determinism'  => ['নিয়ন্ত্রণবাদ',      'নির্ধারণবাদ'],
        'objectivism'  => ['বস্তুবাদ',           'বস্তুনিষ্ঠতাবাদ'],
    ];

    public function up(): void
    {
        try {
            $this->apply();
        } catch (\Throwable $e) {
            Log::error('[bcs-meaning-fix] aborted, nothing was changed: ' . $e->getMessage());
        }
    }

    private function apply(): void
    {
        foreach (['chapters', 'lessons', 'words'] as $t) {
            if (!Schema::hasTable($t)) { return; }
        }
        if (Schema::hasTable(self::BACKUP)) { Log::info('[bcs-meaning-fix] already applied'); return; }

        $chapterIds = DB::table('chapters')->where('title', 'like', 'BCS English%')->pluck('id')->all();
        if (!$chapterIds) { Log::warning('[bcs-meaning-fix] BCS chapters not found, nothing changed'); return; }
        $lessonIds = DB::table('lessons')->whereIn('chapter_id', $chapterIds)->pluck('id')->all();
        if (!$lessonIds) { return; }

        $todo = []; // word id => [old, new, word]
        foreach (DB::table('words')->whereIn('lesson_id', $lessonIds)->get(['id', 'word', 'meaning']) as $w) {
            $key = mb_strtolower(trim((string) $w->word), 'UTF-8');
            if (!isset(self::FIXES[$key])) { continue; }
            [$old, $new] = self::FIXES[$key];
            if ((string) $w->meaning === $old) {
                $todo[$w->id] = [$old, $new, $w->word];
            }
        }
        if (!$todo) { Log::info('[bcs-meaning-fix] nothing to fix'); return; }

        Schema::create(self::BACKUP, function ($t) {
            $t->unsignedBigInteger('word_id')->primary();
            $t->text('old_meaning')->nullable();
        });

        try {
            DB::transaction(function () use ($todo) {
                foreach ($todo as $id => [$old, $new]) {
                    DB::table(self::BACKUP)->insert(['word_id' => $id, 'old_meaning' => $old]);
                    $n = DB::table('words')->where('id', $id)->where('meaning', $old)
                        ->update(['meaning' => $new, 'updated_at' => now()]);
                    if ($n !== 1) { throw new \RuntimeException('word ' . $id . ' changed concurrently'); }
                }
            });
        } catch (\Throwable $e) {
            Schema::dropIfExists(self::BACKUP);
            throw $e;
        }

        $names = array_map(fn ($v) => $v[2], array_values($todo));
        Log::info('[bcs-meaning-fix] corrected ' . count($todo) . ' meanings: ' . implode(', ', $names));
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::BACKUP)) {
            return;
        }
        DB::transaction(function () {
            foreach (DB::table(self::BACKUP)->get() as $r) {
                DB::table('words')->where('id', $r->word_id)->update(['meaning' => $r->old_meaning]);
            }
        });
        Schema::dropIfExists(self::BACKUP);
    }
};
