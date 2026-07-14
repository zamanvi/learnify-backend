<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('unlocked_lessons')) {
        Schema::create('unlocked_lessons', function (Blueprint $table) {
            $table->id();
            // users.id is a legacy unsignedInteger (increments()), not the
            // unsignedBigInteger foreignId() assumes - match its actual type
            // instead of Laravel's modern default (see friends/notifications
            // migrations for the same pattern already used elsewhere here).
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'lesson_id']);
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('unlocked_lessons');
    }
};
