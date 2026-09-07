<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// "Book 2" / website (masterenglishbook.com) chapters. Deliberately a
// separate table from the app's existing `chapters` table (App\Models\
// Chapter) - same conceptual role (Chapter under a top-level grouping) but
// a fully independent structure for the website, so it can never collide
// with or accidentally overwrite live app data/behavior.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('section_id');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('web_chapters');
    }
};
