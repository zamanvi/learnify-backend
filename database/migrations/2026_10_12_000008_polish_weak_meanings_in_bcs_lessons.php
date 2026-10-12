<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Second, smaller pass after 2026_10_12_000007: nine BCS words whose Bangla
 * meaning is vague or points at a different concept (Animosity = "ক্রোধ" is
 * anger, Conscientious = "বিবেকপ্রসূত" is not a real meaning, ...), plus the
 * spelling "Embargoe" -> "Embargo". Where the same word already has a good
 * meaning elsewhere in the app, that same wording is reused.
 *
 * A row is changed ONLY when it sits in a lesson of a chapter titled
 * "BCS English ..." AND its word text matches AND its meaning is still EXACTLY
 * the known old value (a hand-edited meaning is never overwritten).
 *
 * Only words.word (Embargoe) and words.meaning change. Old values go to
 * bcs_meaning_polish_backup for down(). Guarded, single transaction +
 * verification, logged, never throws (cannot block boot).
 */
return new class extends Migration
{
    private const BACKUP = 'bcs_meaning_polish_backup';

    /** old word (lower-case) => [old meaning, new meaning, new word text or null] */
    private const FIXES = [
        'embargoe'      => ['বাণিজ্য বর্জন',      'বাণিজ্য অবরোধ',          'Embargo'],
        'conscientious' => ['বিবেকপ্রসূত',        'বিবেকবান, নিষ্ঠাবান',    null],
        'fastidious'    => ['বিশদ মনোযোগী',      'খুঁতখুঁতে',              null],
        'animosity'     => ['ক্রোধ',              'বিদ্বেষ',                null],
        'elocution'     => ['বক্তৃতা',            'বাচনভঙ্গি',              null],
        'pragmatics'    => ['ব্যবহার সংক্রান্ত',  'প্রয়োগতত্ত্ব',          null],
        'deism'         => ['প্রাকৃতিক ধর্ম',    'যুক্তিভিত্তিক ঈশ্বরবিশ্বাস', null],
        'decompose'     => ['বিয়োজন',            'পচে যাওয়া / বিশ্লিষ্ট হওয়া', null],
        'quota'         => ['পরিমাণ সীমা',       'কোটা / নির্ধারিত অংশ',   null],
    ];

    public function up(): void
    {
        try {
            $this->apply();
        } catch (\Throwable $e) {
            Log::error('[bcs-meaning-polish] aborted, nothing was changed: ' . $e->getMessage());
        }
    }

    private function apply(): void
    {
        foreach (['chapters', 'lessons', 'words'] as $t) {
            if (!Schema::hasTable($t)) { return; }
        }
        if (Schema::hasTable(self::BACKUP)) { Log::info('[bcs-meaning-polish] already applied'); return; }

        $chapterIds = DB::table('chapters')->where('title', 'like', 'BCS English%')->pluck('id')->all();
        if (!$chapterIds) { Log::warning('[bcs-meaning-polish] BCS chapters not found, nothing changed'); return; }
        $lessonIds = DB::table('lessons')->whereIn('chapter_id', $chapterIds)->pluck('id')->all();
        if (!$lessonIds) { return; }

        $todo = []; // word id => [old word, old meaning, new word, new meaning]
        foreach (DB::table('words')->whereIn('lesson_id', $lessonIds)->get(['id', 'word', 'meaning']) as $w) {
            $key = mb_strtolower(trim((string) $w->word), 'UTF-8');
            if (!isset(self::FIXES[$key])) { continue; }
            [$oldMeaning, $newMeaning, $newWord] = self::FIXES[$key];
            if ((string) $w->meaning === $oldMeaning) {
                $todo[$w->id] = [(string) $w->word, $oldMeaning, $newWord ?? (string) $w->word, $newMeaning];
            }
        }
        if (!$todo) { Log::info('[bcs-meaning-polish] nothing to change'); return; }

        Schema::create(self::BACKUP, function ($t) {
            $t->unsignedBigInteger('word_id')->primary();
            $t->string('old_word');
            $t->text('old_meaning')->nullable();
        });

        try {
            DB::transaction(function () use ($todo) {
                foreach ($todo as $id => [$oldWord, $oldMeaning, $newWord, $newMeaning]) {
                    DB::table(self::BACKUP)->insert(['word_id' => $id, 'old_word' => $oldWord, 'old_meaning' => $oldMeaning]);
                    $n = DB::table('words')->where('id', $id)->where('word', $oldWord)->where('meaning', $oldMeaning)
                        ->update(['word' => $newWord, 'meaning' => $newMeaning, 'updated_at' => now()]);
                    if ($n !== 1) { throw new \RuntimeException('word ' . $id . ' changed concurrently'); }
                }
            });
        } catch (\Throwable $e) {
            Schema::dropIfExists(self::BACKUP);
            throw $e;
        }

        $names = array_map(fn ($v) => $v[2], array_values($todo));
        Log::info('[bcs-meaning-polish] updated ' . count($todo) . ' rows: ' . implode(', ', $names));
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::BACKUP)) {
            return;
        }
        DB::transaction(function () {
            foreach (DB::table(self::BACKUP)->get() as $r) {
                DB::table('words')->where('id', $r->word_id)
                    ->update(['word' => $r->old_word, 'meaning' => $r->old_meaning]);
            }
        });
        Schema::dropIfExists(self::BACKUP);
    }
};
