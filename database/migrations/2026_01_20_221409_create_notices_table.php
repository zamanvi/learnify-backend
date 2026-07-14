<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('notices')) {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('app', ['grammar', 'paragraph'])->default('grammar');
            $table->string('body');
            $table->string('slug')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};
