<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Tracks which BookChapter a WebChapter was copied from (nullable - only set
// when created via the "Copy from Book Chapter" tool). Purely additive to
// the already-independent web_chapters table; does not touch book_chapters
// in any way. Lets bulk-copy skip chapters already copied into a section.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_chapters', function (Blueprint $table) {
            $table->unsignedBigInteger('source_book_chapter_id')->nullable()->after('section_id');
            $table->index('source_book_chapter_id');
        });
    }

    public function down(): void
    {
        Schema::table('web_chapters', function (Blueprint $table) {
            $table->dropColumn('source_book_chapter_id');
        });
    }
};
