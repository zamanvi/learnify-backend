<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Group tournament fairness: a group's ranking used to be every member's
 * lifetime XP, so a newcomer with 10,000 XP "won" a class of beginners.
 * The ranking now counts XP earned SINCE JOINING (users.points minus the
 * balance remembered here at join time).
 *
 * Existing members are given their current balance, i.e. every current
 * group starts level from the moment this runs. Only group_members gains a
 * column; users.points and every other table are untouched. The migration
 * never throws (cannot block boot) and down() drops only the new column.
 */
return new class extends Migration
{
    public function up(): void
    {
        try {
            if (!Schema::hasTable('group_members') || Schema::hasColumn('group_members', 'points_at_join')) {
                return;
            }
            Schema::table('group_members', function ($table) {
                $table->unsignedInteger('points_at_join')->default(0)->after('user_id');
            });
            DB::statement(
                'UPDATE group_members gm JOIN users u ON u.id = gm.user_id SET gm.points_at_join = GREATEST(u.points, 0)'
            );
        } catch (\Throwable $e) {
            Log::error('[group-fix] points_at_join migration problem: ' . $e->getMessage());
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('group_members') && Schema::hasColumn('group_members', 'points_at_join')) {
            Schema::table('group_members', function ($table) {
                $table->dropColumn('points_at_join');
            });
        }
    }
};
