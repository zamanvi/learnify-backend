<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Tracks, per user, when a word was last shown in the round-based Quick
    // Quiz (GameController::roundQuiz()) - lets QuizQuestionBuilder favor
    // least-recently-shown words instead of pure ORDER BY RAND(), so a
    // lesson's whole word pool rotates through before anything repeats.
    // Deliberately NOT used by BattleController or the legacy quiz() -
    // those keep using the existing pure-random selectWordIds().
    public function up(): void
    {
        if (Schema::hasTable('user_word_exposures')) return;

        Schema::create('user_word_exposures', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('word_id');
            $table->foreign('word_id')->references('id')->on('words')->onDelete('cascade');
            $table->timestamp('last_shown_at');

            $table->unique(['user_id', 'word_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_word_exposures');
    }
};
