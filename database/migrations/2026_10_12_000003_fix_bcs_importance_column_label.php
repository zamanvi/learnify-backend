<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * BCS English lessons store an importance % (95..45) in the words.antonyms
 * column, but several lessons still label that column "English Synonym", so the
 * app shows "90" under an "ENGLISH SYNONYM" header.
 *
 * For every lesson of the "BCS English" chapter whose antonyms values are ALL
 * numeric and whose col4_label is still the English-Synonym label, this sets
 * col4_label to the honest "importance %" label. Lessons with any non-numeric
 * value are left alone.
 *
 * Labels only: no words, no other columns, no deletes. Guarded + logged, never
 * throws (cannot block boot), old labels kept in bcs_label_fix_backup for down().
 */
return new class extends Migration
{
    private const BACKUP = 'bcs_label_fix_backup';
    private const OLD    = '🔤 English Synonym';
    private const NEW    = '📊 গুরুত্ব %';

    public function up(): void
    {
        try {
            $this->fix();
        } catch (\Throwable $e) {
            Log::error('[bcs-label-fix] aborted, nothing was changed: ' . $e->getMessage());
        }
    }

    private function fix(): void
    {
        foreach (['chapters', 'lessons', 'words'] as $t) {
            if (!Schema::hasTable($t)) { return; }
        }
        if (!Schema::hasColumn('lessons', 'col4_label')) { return; }
        if (Schema::hasTable(self::BACKUP)) { Log::info('[bcs-label-fix] already applied'); return; }

        $chapter = DB::table('chapters')->where('title', 'like', 'BCS English%')->orderBy('id')->first();
        if (!$chapter) { Log::warning('[bcs-label-fix] BCS chapter not found, nothing changed'); return; }

        $todo = [];
        foreach (DB::table('lessons')->where('chapter_id', $chapter->id)->where('col4_label', self::OLD)->get() as $l) {
            $values = DB::table('words')->where('lesson_id', $l->id)->pluck('antonyms')->all();
            if (!$values) { continue; }
            $allNumeric = true;
            foreach ($values as $v) {
                if (!ctype_digit(trim((string) $v))) { $allNumeric = false; break; }
            }
            if ($allNumeric) { $todo[] = $l->id; }
        }
        if (!$todo) { Log::info('[bcs-label-fix] nothing to fix'); return; }

        Schema::create(self::BACKUP, function ($t) {
            $t->unsignedBigInteger('lesson_id')->primary();
            $t->string('old_col4_label')->nullable();
        });

        try {
            DB::transaction(function () use ($todo) {
                foreach ($todo as $id) {
                    DB::table(self::BACKUP)->insert(['lesson_id' => $id, 'old_col4_label' => self::OLD]);
                    DB::table('lessons')->where('id', $id)->where('col4_label', self::OLD)
                        ->update(['col4_label' => self::NEW, 'updated_at' => now()]);
                }
            });
        } catch (\Throwable $e) {
            Schema::dropIfExists(self::BACKUP);
            throw $e;
        }

        Log::info('[bcs-label-fix] relabelled ' . count($todo) . ' lessons: ' . implode(',', $todo));
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::BACKUP)) {
            return;
        }
        foreach (DB::table(self::BACKUP)->get() as $r) {
            DB::table('lessons')->where('id', $r->lesson_id)->update(['col4_label' => $r->old_col4_label]);
        }
        Schema::dropIfExists(self::BACKUP);
    }
};
