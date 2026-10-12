<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Four wrong values in the exam-vocabulary chapter (both halves, after
 * 2026_10_12_000009):
 *
 *   Condescend  meaning  "নমনীয় হওয়া"  -> "উঁচু ভাব দেখিয়ে সদয় আচরণ করা"
 *   Heresy      synonym  "blasphemy"    -> "heterodoxy"   (not the same thing)
 *   Empirical   synonym  "practical"    -> "observational"
 *   Unanimous   synonym  "agreed"       -> "united"
 *
 * (In these lessons the English synonym is stored in words.antonyms.)
 *
 * A row is changed ONLY when it sits in a lesson of the chapter, its word
 * text matches and the field still holds EXACTLY the known old value, so a
 * value an editor already corrected is never overwritten. Nothing else changes.
 * Old values go to adm_fix_backup for down(). Guarded, one transaction +
 * verification, logged, never throws (cannot block boot).
 */
return new class extends Migration
{
    private const BACKUP = 'adm_fix_backup';

    private const TITLES = [
        '📝 পরীক্ষার English — ভর্তি ও চাকরির প্রস্তুতি',
        '📝 Admission & Job English — পরীক্ষায় এসেছে',
        '📝 Admission & Job English — ভবিষ্যৎ পরীক্ষায় আসতে পারে',
    ];

    /** word (lower-case) => [column, old value, new value] */
    private const FIXES = [
        'condescend' => ['meaning',  'নমনীয় হওয়া', 'উঁচু ভাব দেখিয়ে সদয় আচরণ করা'],
        'heresy'     => ['antonyms', 'blasphemy',    'heterodoxy'],
        'empirical'  => ['antonyms', 'practical',    'observational'],
        'unanimous'  => ['antonyms', 'agreed',       'united'],
    ];

    public function up(): void
    {
        try {
            $this->apply();
        } catch (\Throwable $e) {
            Log::error('[adm-fix] aborted, nothing was changed: ' . $e->getMessage());
        }
    }

    private function apply(): void
    {
        foreach (['chapters', 'lessons', 'words'] as $t) {
            if (!Schema::hasTable($t)) { return; }
        }
        if (Schema::hasTable(self::BACKUP)) { Log::info('[adm-fix] already applied'); return; }

        $chapterIds = DB::table('chapters')->whereIn('title', self::TITLES)->pluck('id')->all();
        if (!$chapterIds) { Log::warning('[adm-fix] chapters not found, nothing changed'); return; }
        $lessonIds = DB::table('lessons')->whereIn('chapter_id', $chapterIds)->pluck('id')->all();
        if (!$lessonIds) { return; }

        $todo = []; // word id => [column, old, new, word]
        foreach (DB::table('words')->whereIn('lesson_id', $lessonIds)->get(['id', 'word', 'meaning', 'antonyms']) as $w) {
            $key = mb_strtolower(trim((string) $w->word), 'UTF-8');
            if (!isset(self::FIXES[$key])) { continue; }
            [$col, $old, $new] = self::FIXES[$key];
            if ((string) $w->{$col} === $old) {
                $todo[$w->id] = [$col, $old, $new, $w->word];
            }
        }
        if (!$todo) { Log::info('[adm-fix] nothing to fix'); return; }

        Schema::create(self::BACKUP, function ($t) {
            $t->unsignedBigInteger('word_id')->primary();
            $t->string('col', 20);
            $t->text('old_value')->nullable();
        });

        try {
            DB::transaction(function () use ($todo) {
                foreach ($todo as $id => [$col, $old, $new]) {
                    DB::table(self::BACKUP)->insert(['word_id' => $id, 'col' => $col, 'old_value' => $old]);
                    $n = DB::table('words')->where('id', $id)->where($col, $old)
                        ->update([$col => $new, 'updated_at' => now()]);
                    if ($n !== 1) { throw new \RuntimeException('word ' . $id . ' changed concurrently'); }
                }
            });
        } catch (\Throwable $e) {
            Schema::dropIfExists(self::BACKUP);
            throw $e;
        }

        Log::info('[adm-fix] corrected ' . count($todo) . ' values: ' . implode(', ', array_map(fn ($v) => $v[3], array_values($todo))));
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::BACKUP)) {
            return;
        }
        DB::transaction(function () {
            foreach (DB::table(self::BACKUP)->get() as $r) {
                DB::table('words')->where('id', $r->word_id)->update([$r->col => $r->old_value]);
            }
        });
        Schema::dropIfExists(self::BACKUP);
    }
};
