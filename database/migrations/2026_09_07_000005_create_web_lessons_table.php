<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// "Book 2" / website lessons, under web_chapters. Separate from the app's
// existing `lessons` table (App\Models\Lesson) for the same reason as
// web_chapters - see that migration's comment.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('web_chapter_id')->constrained('web_chapters')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('web_chapter_id');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_lessons');
    }
};
