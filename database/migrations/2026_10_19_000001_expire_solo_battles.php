<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Battles of the "customize / join by code" kind used to be marked completed
 * - with their creator as the winner - the moment the creator finished, even
 * when nobody had joined. Those fake wins ("জয়" against "?") are marked
 * "expired" with no winner. Only battles that are completed, have exactly one
 * participant row and a winner are touched; the old values go to
 * battle_expire_backup and down() restores them. One transaction; never
 * throws (cannot block boot).
 */
return new class extends Migration
{
    private const BACKUP = 'battle_expire_backup';

    public function up(): void
    {
        try {
            $this->apply();
        } catch (\Throwable $e) {
            Log::error('[battle-expire] aborted, nothing was changed: ' . $e->getMessage());
        }
    }

    private function apply(): void
    {
        foreach (['battles', 'battle_participants'] as $t) {
            if (!Schema::hasTable($t)) { return; }
        }
        if (Schema::hasTable(self::BACKUP)) { Log::info('[battle-expire] already applied'); return; }

        $ids = DB::table('battles')
            ->where('status', 'completed')
            ->whereIn('id', function ($q) {
                $q->select('battle_id')->from('battle_participants')->groupBy('battle_id')->havingRaw('count(*) = 1');
            })
            ->pluck('id');

        if ($ids->isEmpty()) { Log::info('[battle-expire] nothing to expire'); return; }

        Schema::create(self::BACKUP, function ($t) {
            $t->unsignedBigInteger('battle_id')->primary();
            $t->string('old_status', 20);
            $t->unsignedInteger('old_winner_id')->nullable();
        });

        try {
            DB::transaction(function () use ($ids) {
                foreach (DB::table('battles')->whereIn('id', $ids)->get(['id', 'status', 'winner_id']) as $b) {
                    DB::table(self::BACKUP)->insert([
                        'battle_id' => $b->id, 'old_status' => $b->status, 'old_winner_id' => $b->winner_id,
                    ]);
                }
                DB::table('battles')->whereIn('id', $ids)->where('status', 'completed')
                    ->update(['status' => 'expired', 'winner_id' => null]);
            });
        } catch (\Throwable $e) {
            Schema::dropIfExists(self::BACKUP);
            throw $e;
        }

        Log::info('[battle-expire] battles expired: ' . $ids->count());
    }

    public function down(): void
    {
        if (!Schema::hasTable(self::BACKUP)) { return; }
        DB::transaction(function () {
            foreach (DB::table(self::BACKUP)->get() as $r) {
                DB::table('battles')->where('id', $r->battle_id)
                    ->update(['status' => $r->old_status, 'winner_id' => $r->old_winner_id]);
            }
        });
        Schema::dropIfExists(self::BACKUP);
    }
};
