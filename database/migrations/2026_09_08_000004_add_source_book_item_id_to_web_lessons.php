<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Same idea as source_book_chapter_id on web_chapters - tracks which
// BookItem a WebLesson was copied from, for dedup/traceability. Purely
// additive to the already-independent web_lessons table.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_lessons', function (Blueprint $table) {
            $table->unsignedBigInteger('source_book_item_id')->nullable()->after('web_chapter_id');
            $table->index('source_book_item_id');
        });
    }

    public function down(): void
    {
        Schema::table('web_lessons', function (Blueprint $table) {
            $table->dropColumn('source_book_item_id');
        });
    }
};
