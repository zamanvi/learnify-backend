<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_lesson_round_progress')) return;

        Schema::create('user_lesson_round_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('lesson_id');
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');

            // 1 = MCQ, 2 = Reading, 3 = Listening, 4 = Writing
            $table->unsignedTinyInteger('round_number');

            // locked | unlocked | passed
            $table->string('status', 20)->default('locked');

            $table->unsignedTinyInteger('stars')->default(0);
            $table->unsignedTinyInteger('best_score')->nullable();
            $table->unsignedTinyInteger('best_total')->nullable();
            $table->unsignedTinyInteger('hearts_lost_best_attempt')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'lesson_id', 'round_number']);
            $table->index(['user_id', 'lesson_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_lesson_round_progress');
    }
};
