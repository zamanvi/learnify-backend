<?php

use App\Models\Book;
use Illuminate\Database\Migrations\Migration;

// "Master English Book Part" (book_id=1, slug='master-english-book-part-i') is now completely
// orphaned:
// - Website was using it for content display, but now uses WebChapter/WebLesson (Website Sections)
//   exclusively. All 122 lessons from this book were copied to Website Sections Bangladesh in
//   migration 2026_09_08_000010.
// - App (English Grammar Book) only ever reads the "english-hub" book for grammar/speaking/writing
//   modes and has never touched this book (confirmed via audit 2026-09-08).
// - No other code path references this book or its related BookChapter/BookItem rows.
// ON DELETE CASCADE on foreign keys means deleting this book will orphan no data anywhere else.
// This deletion is 100% safe and completes the transition from Book model to WebChapter/WebLesson
// for website content.

return new class extends Migration
{
    public function up(): void
    {
        // Delete Master English Book Part (slug='master-english-book-part-i')
        // All related BookChapter and BookItem rows will cascade-delete automatically
        Book::where('slug', 'master-english-book-part-i')->delete();
    }

    public function down(): void
    {
        // Not reversible - this book and its structure are no longer needed.
        // Website Sections copies are preserved separately in WebChapter/WebLesson tables.
    }
};
