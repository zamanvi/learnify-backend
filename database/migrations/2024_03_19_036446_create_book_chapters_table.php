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
        if (!Schema::hasTable('book_chapters')) {
        Schema::create('book_chapters', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['web', 'grammar', 'daily_vocabulary', 'writing_reading'])->default('web');
            $table->string('slug')->nullable()->unique();
            $table->boolean('status')->default(true);
            $table->integer('pageview')->default(0);
            $table->string('added_by');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_chapters');
    }
};
