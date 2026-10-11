<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Adds lessons.layout (nullable): how the app lays out this lesson's word list.
 *   NULL      automatic - the app decides from the lesson's own words (default
 *             for every existing and future lesson)
 *   'stacked' word above its meaning on every row
 *   'compact' word and meaning side by side on every row
 *
 * Existing lessons stay NULL (no change in behaviour). The lessons of the two
 * "BCS English" chapters are set to 'stacked' so the whole BCS family looks the
 * same. Admins can change it per lesson in the lesson form.
 *
 * Safety: additive nullable column; only lessons.layout of BCS lessons is
 * written; never throws (cannot block boot); down() restores exactly.
 */
return new class extends Migration
{
    private const BACKUP = 'bcs_layout_backup';

    public function up(): void
    {
        try {
            if (!Schema::hasTable('lessons')) { return; }

            $created = false;
            if (!Schema::hasColumn('lessons', 'layout')) {
                Schema::table('lessons', function (Blueprint $t) {
                    $t->string('layout', 10)->nullable();
                });
                $created = true;
            }

            if (!Schema::hasTable('chapters') || Schema::hasTable(self::BACKUP)) { return; }

            $chapterIds = DB::table('chapters')->where('title', 'like', 'BCS English%')->pluck('id')->all();
            $lessonIds  = $chapterIds
                ? DB::table('lessons')->whereIn('chapter_id', $chapterIds)->whereNull('layout')->pluck('id')->all()
                : [];

            Schema::create(self::BACKUP, function (Blueprint $t) {
                $t->unsignedBigInteger('lesson_id')->primary();
                $t->boolean('column_created')->default(false);
            });
            DB::table(self::BACKUP)->insert(['lesson_id' => 0, 'column_created' => $created]);

            DB::transaction(function () use ($lessonIds) {
                foreach (array_chunk($lessonIds, 100) as $chunk) {
                    DB::table(self::BACKUP)->insert(array_map(fn ($id) => ['lesson_id' => $id, 'column_created' => false], $chunk));
                    DB::table('lessons')->whereIn('id', $chunk)->update(['layout' => 'stacked']);
                }
            });

            Log::info('[lesson-layout] column ' . ($created ? 'added' : 'already present') . '; '
                . count($lessonIds) . ' BCS lessons set to stacked');
        } catch (\Throwable $e) {
            Log::error('[lesson-layout] problem, boot continues: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        if (Schema::hasTable(self::BACKUP)) {
            $ids = DB::table(self::BACKUP)->where('lesson_id', '>', 0)->pluck('lesson_id')->all();
            if (Schema::hasColumn('lessons', 'layout') && $ids) {
                foreach (array_chunk($ids, 100) as $chunk) {
                    DB::table('lessons')->whereIn('id', $chunk)->update(['layout' => null]);
                }
            }
            $created = (bool) DB::table(self::BACKUP)->where('lesson_id', 0)->value('column_created');
            Schema::dropIfExists(self::BACKUP);
            if ($created && Schema::hasColumn('lessons', 'layout')) {
                Schema::table('lessons', function (Blueprint $t) {
                    $t->dropColumn('layout');
                });
            }
        }
    }
};
