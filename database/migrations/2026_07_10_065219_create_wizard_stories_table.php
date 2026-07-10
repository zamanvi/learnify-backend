<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wizard_stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained('wizard_chapters')->onDelete('cascade');
            $table->string('hook_title');
            $table->string('meta')->nullable();
            $table->json('english_paragraphs');
            $table->string('bangla_title');
            $table->json('bangla_paragraphs');
            $table->json('grammar_notes')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('order_by')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wizard_stories');
    }
};
