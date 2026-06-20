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
        Schema::create('book_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('sub_title')->nullable();
            $table->string('type');
            $table->longText('short_details');
            $table->longText('details')->nullable();
            $table->text('link')->nullable();
            $table->longText('audio_text')->nullable();
            $table->longText('keyword')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->boolean('status')->default(true);
            $table->integer('pageview')->default(0);
            $table->string('added_by');
            $table->foreignId('chapter_id')->constrained('book_chapters')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_items');
    }
};
