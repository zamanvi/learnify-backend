<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // doctrine/dbal isn't installed in this project (confirmed: no
        // "doctrine/dbal" package entry in composer.lock, only referenced
        // transitively by other packages' "suggest" sections) - Schema's
        // fluent ->change() requires it and would fatal on deploy, so the
        // opponent_id nullability change goes through raw SQL instead.
        DB::statement('ALTER TABLE battles MODIFY opponent_id INT UNSIGNED NULL');

        Schema::table('battles', function (Blueprint $table) {
            // 'async' (today's behavior) | 'live' (Phase 2, rejected by
            // BattleController::create() for now but reserved so a later
            // real-time mode doesn't need another breaking migration.
            $table->string('mode', 10)->default('async')->after('lesson_id');

            $table->unsignedTinyInteger('question_count')->nullable()->after('mode');
            $table->unsignedTinyInteger('lives')->nullable()->after('question_count');

            // Fixed word IDs used to build this battle's quiz, so every
            // participant (2 or N) sees the exact same question set.
            $table->json('question_word_ids')->nullable()->after('lives');

            $table->string('invite_code', 8)->nullable()->unique()->after('question_word_ids');
            $table->unsignedTinyInteger('max_participants')->nullable()->after('invite_code');
        });
    }

    public function down(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            $table->dropColumn([
                'mode', 'question_count', 'lives', 'question_word_ids',
                'invite_code', 'max_participants',
            ]);
        });

        // Only safe to restore NOT NULL if no new-shape (null-opponent) rows
        // exist - otherwise this intentionally fails loudly rather than
        // silently corrupting/deleting battles on a rollback.
        DB::statement('ALTER TABLE battles MODIFY opponent_id INT UNSIGNED NOT NULL');
    }
};
