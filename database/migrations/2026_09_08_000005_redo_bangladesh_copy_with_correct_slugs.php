<?php

use App\Models\Book;
use App\Models\BookChapter;
use App\Models\Section;
use App\Models\WebChapter;
use App\Models\WebLesson;
use Illuminate\Database\Migrations\Migration;

// The earlier bulk copy regenerated slugs from title instead of copying the
// Book's own slug, and copied unpublished items too - 23 of 56 lessons ended
// up with slugs that don't match the currently-indexed /book/{slug} URLs
// (many BookItem/BookChapter slugs are hand-shortened, not just
// make_slug(title)), which would have broken SEO once the frontend switched
// to this data. WebChapter::copyFromBookChapter now fixes both issues.
// This removes every auto-copied (source_book_chapter_id NOT NULL) chapter
// in Bangladesh and redoes the copy with the corrected logic. Strictly
// read-only against Book/BookChapter/BookItem throughout.
return new class extends Migration
{
    public function up(): void
    {
        $section = Section::where('slug', 'bangladesh')->first();
        if (!$section) {
            return;
        }

        $autoCopiedChapterIds = WebChapter::where('section_id', $section->id)
            ->whereNotNull('source_book_chapter_id')
            ->pluck('id');

        WebLesson::whereIn('web_chapter_id', $autoCopiedChapterIds)->delete();
        WebChapter::whereIn('id', $autoCopiedChapterIds)->delete();

        $book = Book::where('slug', 'master-english-book-part-i')->first();
        if (!$book) {
            return;
        }

        $bookChapters = BookChapter::with('items')
            ->where('book_id', $book->id)
            ->where('status', true)
            ->get();

        foreach ($bookChapters as $bookChapter) {
            WebChapter::copyFromBookChapter($bookChapter, $section->id);
        }
    }

    public function down(): void
    {
        // Not reversible - re-run the (now corrected) copy is the recovery path.
    }
};
