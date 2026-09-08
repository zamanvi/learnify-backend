<?php

use App\Models\WebChapter;
use App\Models\WebLesson;
use Illuminate\Database\Migrations\Migration;

// Removes the "Test Chapter - Verification" chapter (+ its lesson) created
// while manually verifying the Website Sections feature earlier - pure
// placeholder junk, not real site content, and was showing live via the
// public API. Scoped tightly by exact title + null source_book_chapter_id
// so it can never match anything else.
return new class extends Migration
{
    public function up(): void
    {
        $testChapter = WebChapter::where('title', 'Test Chapter - Verification')
            ->whereNull('source_book_chapter_id')
            ->first();

        if ($testChapter) {
            WebLesson::where('web_chapter_id', $testChapter->id)->delete();
            $testChapter->delete();
        }
    }

    public function down(): void
    {
        // Not reversible - this only ever deletes known placeholder test data.
    }
};
