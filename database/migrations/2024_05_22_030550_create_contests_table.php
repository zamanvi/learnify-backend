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
        Schema::create('contests', function (Blueprint $table) {
            $table->id();
            $table->longText('name');
            $table->string('date');
            $table->string('time');
            $table->boolean('status')->default(true);
            $table->integer('price');
            $table->string('duration');
            $table->string('image_path')->nullable();
            $table->string('syllabus_title')->nullable();
            $table->longText('description')->nullable();
            $table->string('slug');
            $table->string('u_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contests');
    }
};
