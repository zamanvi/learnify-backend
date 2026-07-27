<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('battle_participants')) return;

        Schema::create('battle_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('battle_id');
            $table->foreign('battle_id')->references('id')->on('battles')->onDelete('cascade');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->boolean('is_creator')->default(false);

            $table->unsignedTinyInteger('score')->nullable();
            $table->unsignedTinyInteger('total')->nullable();
            $table->unsignedSmallInteger('time_sec')->nullable();
            $table->unsignedTinyInteger('lives_remaining')->nullable();

            // invited | joined | submitted
            $table->string('status', 20)->default('invited');

            $table->timestamp('joined_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->unique(['battle_id', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index('battle_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('battle_participants');
    }
};
