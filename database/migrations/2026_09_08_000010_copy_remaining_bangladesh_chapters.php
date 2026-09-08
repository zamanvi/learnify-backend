<?php

use App\Models\Book;
use App\Models\BookChapter;
use App\Models\Section;
use App\Models\WebChapter;
use Illuminate\Database\Migrations\Migration;

// "Master English Book Part" has 20 chapters total; the 2026-09-08 copy
// migrations above only ever copied the 15 with status=true. The other 5
// (paragraph, composition, essay, the book's intro chapter, and an empty
// "Aplications" chapter) hold 122 real, never-published lessons that exist
// nowhere else - confirmed via audit that nothing else references them (not
// the app, which only ever reads the separate "english-hub" book; not the
// frontend, which no longer imports the old Book-based fetch helpers).
// User-requested: bring them into Bangladesh via the same
// WebChapter::copyFromBookChapter path the "Copy from Book Chapter" admin
// button uses, so this is byte-identical to doing it by hand. They go live
// immediately (copyFromBookChapter sets is_active=true), same as the manual
// path would. Strictly read-only against Book/BookChapter/BookItem.
return new class extends Migration
{
    public function up(): void
    {
        $section = Section::where('slug', 'bangladesh')->first();
        $book = Book::where('slug', 'master-english-book-part-i')->first();
        if (!$section || !$book) {
            return;
        }

        $alreadyCopiedIds = WebChapter::where('section_id', $section->id)
            ->whereNotNull('source_book_chapter_id')
            ->pluck('source_book_chapter_id')
            ->all();

        $slugs = [
            'master-english-book',
            'food-adulteration-paragraph',
            'essay-1-90ehgbmoat-91-qRAUim3zt3',
            'email',
            'aplications',
        ];

        $bookChapters = BookChapter::with('items')
            ->where('book_id', $book->id)
            ->whereIn('slug', $slugs)
            ->whereNotIn('id', $alreadyCopiedIds)
            ->get();

        foreach ($bookChapters as $bookChapter) {
            WebChapter::copyFromBookChapter($bookChapter, $section->id);
        }
    }

    public function down(): void
    {
        // Not reversible - hide/delete the copied chapters from the
        // Website Sections admin panel by hand if this needs undoing.
    }
};
