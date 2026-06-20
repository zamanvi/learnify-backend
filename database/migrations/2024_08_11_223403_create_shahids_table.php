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
        Schema::create('shahids', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->text('description');
            $table->string('thumbnail_path');
            $table->text('gallery_path')->nullable();
            $table->text('video_link')->nullable();
            $table->integer('pageview')->default(0);
            $table->boolean('status')->default(true);
            $table->string('slug')->unique();
            $table->string('u_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shahids');
    }
};
