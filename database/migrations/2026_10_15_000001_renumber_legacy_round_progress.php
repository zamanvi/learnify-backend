<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Quick Quiz progress written before the Picture round existed uses the old
 * four-round numbering (1 MCQ, 2 Reading, 3 Listening, 4 Writing). The server
 * has numbered the rounds 1 MCQ, 2 Reading, 3 Picture, 4 Listening,
 * 5 Writing since 2026-08-18 (commit 157c22d) but nobody converted those old
 * rows, so e.g. "round 3 passed" really means "Listening passed".
 *
 * Rows for rounds 3 and 4 created before 2026-08-19 are shifted to 4 and 5.
 * Where the target slot already holds a row (healing may have created one)
 * the two are merged, keeping the better status / stars. Rounds 1-2 and
 * every later row are untouched. Every changed row is copied to
 * round_progress_renumber_backup (full JSON) and down() puts them back.
 * One transaction; the migration never throws (cannot block boot).
 */
return new class extends Migration
{
    private const TABLE = 'user_lesson_round_progress';
    private const BACKUP = 'round_progress_renumber_backup';
    private const CUTOFF = '2026-08-19 00:00:00';
    private const RANK = ['locked' => 1, 'unlocked' => 2, 'passed' => 3];

    public function up(): void
    {
        try {
            $this->apply();
        } catch (\Throwable $e) {
            Log::error('[round-renumber] aborted, nothing was changed: ' . $e->getMessage());
        }
    }

    private function apply(): void
    {
        if (!Schema::hasTable(self::TABLE)) { return; }
        if (Schema::hasTable(self::BACKUP)) { Log::info('[round-renumber] already applied'); return; }

        $legacy = DB::table(self::TABLE)->whereIn('round_number', [3, 4])->where('created_at', '<', self::CUTOFF)->count();
        if ($legacy === 0) { Log::info('[round-renumber] no legacy rows'); return; }

        Schema::create(self::BACKUP, function ($t) {
            $t->id();
            $t->unsignedBigInteger('seq');            // order the changes were made in
            $t->string('action', 10);                 // moved | merged
            $t->unsignedTinyInteger('from_round');
            $t->longText('source_row');               // the old row, before the change
            $t->longText('target_row')->nullable();   // the row it was merged into, before the change
        });

        $moved = 0;
        $merged = 0;
        $seq = 0;

        try {
            DB::transaction(function () use (&$moved, &$merged, &$seq) {
                foreach ([4, 3] as $from) { // 4 first, so 3 -> 4 never meets an old row 4
                    $rows = DB::table(self::TABLE)->where('round_number', $from)
                        ->where('created_at', '<', self::CUTOFF)->orderBy('id')->get();
                    foreach ($rows as $row) {
                        $target = DB::table(self::TABLE)->where('user_id', $row->user_id)
                            ->where('lesson_id', $row->lesson_id)->where('round_number', $from + 1)->first();

                        if (!$target) {
                            DB::table(self::BACKUP)->insert([
                                'seq' => ++$seq, 'action' => 'moved', 'from_round' => $from,
                                'source_row' => json_encode($row, JSON_UNESCAPED_UNICODE), 'target_row' => null,
                            ]);
                            DB::table(self::TABLE)->where('id', $row->id)->update(['round_number' => $from + 1]);
                            $moved++;
                            continue;
                        }

                        DB::table(self::BACKUP)->insert([
                            'seq' => ++$seq, 'action' => 'merged', 'from_round' => $from,
                            'source_row' => json_encode($row, JSON_UNESCAPED_UNICODE),
                            'target_row' => json_encode($target, JSON_UNESCAPED_UNICODE),
                        ]);
                        $better = (self::RANK[$row->status] ?? 0) > (self::RANK[$target->status] ?? 0) ? $row : $target;
                        $best = ($row->best_score ?? -1) > ($target->best_score ?? -1) ? $row : $target;
                        DB::table(self::TABLE)->where('id', $target->id)->update([
                            'status'                   => $better->status,
                            'stars'                    => max((int) $row->stars, (int) $target->stars),
                            'best_score'               => $best->best_score,
                            'best_total'               => $best->best_total,
                            'hearts_lost_best_attempt' => $best->hearts_lost_best_attempt,
                            'attempts'                 => (int) $row->attempts + (int) $target->attempts,
                            'last_attempt_at'          => max((string) $row->last_attempt_at, (string) $target->last_attempt_at) ?: null,
                        ]);
                        DB::table(self::TABLE)->where('id', $row->id)->delete();
                        $merged++;
                    }
                }
            });
        } catch (\Throwable $e) {
            Schema::dropIfExists(self::BACKUP);
            throw $e;
        }

        Log::info("[round-renumber] rows moved: {$moved}, merged into an existing row: {$merged}");
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::BACKUP)) { return; }
        DB::transaction(function () {
            foreach (DB::table(self::BACKUP)->orderByDesc('seq')->get() as $b) {
                $source = (array) json_decode($b->source_row, true);
                if ($b->action === 'moved') {
                    DB::table(self::TABLE)->where('id', $source['id'])->update(['round_number' => $b->from_round]);
                } else {
                    $target = (array) json_decode($b->target_row, true);
                    DB::table(self::TABLE)->where('id', $target['id'])->update($target);
                    DB::table(self::TABLE)->insert($source);
                }
            }
        });
        Schema::dropIfExists(self::BACKUP);
    }
};
