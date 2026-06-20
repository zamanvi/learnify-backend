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
        Schema::create('scholar_ships', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->string('title');
            $table->integer('price')->default(0);
            $table->integer('enroll_limit')->default(1);
            $table->integer('winner_limit')->default(1);
            $table->string('date');
            $table->string('time');
            $table->string('slug')->unique();
            $table->boolean('is_publish')->default(false);
            $table->longText('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('sponsor')->nullable();
            $table->string('sponsor_image_path')->nullable();
            $table->string('s_country')->nullable();
            $table->string('s_division')->nullable();
            $table->string('s_city')->nullable();
            $table->string('s_upazila')->nullable();
            $table->bigInteger('pageview')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholar_ships');
    }
};
