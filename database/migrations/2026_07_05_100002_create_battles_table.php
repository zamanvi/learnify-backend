<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('battles')) return;

        Schema::create('battles', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('challenger_id');
            $table->foreign('challenger_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('opponent_id');
            $table->foreign('opponent_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('lesson_id');

            // challenger result
            $table->unsignedTinyInteger('challenger_score')->nullable();
            $table->unsignedTinyInteger('challenger_total')->nullable();
            $table->unsignedSmallInteger('challenger_time_sec')->nullable(); // seconds taken

            // opponent result
            $table->unsignedTinyInteger('opponent_score')->nullable();
            $table->unsignedTinyInteger('opponent_total')->nullable();
            $table->unsignedSmallInteger('opponent_time_sec')->nullable();

            // status: pending | challenger_done | completed | expired
            $table->string('status', 20)->default('pending');
            $table->unsignedInteger('winner_id')->nullable();

            $table->timestamps();

            $table->index(['challenger_id', 'status']);
            $table->index(['opponent_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('battles');
    }
};
