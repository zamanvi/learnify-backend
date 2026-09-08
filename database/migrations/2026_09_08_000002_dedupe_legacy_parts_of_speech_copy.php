<?php

use App\Models\WebChapter;
use App\Models\WebLesson;
use Illuminate\Database\Migrations\Migration;

// One-time data fix: the very first "Copy from Book Chapter" run (before
// source_book_chapter_id existed) created a WebChapter for "Parts of Speech"
// with no source tracking. The later bulk "copy all" couldn't recognize it
// as already-copied (its source_book_chapter_id was null) and made a second,
// properly-tracked copy. This removes the untracked legacy duplicate (+ its
// lessons), keeping the properly-tracked copy. Scoped tightly (title +
// section + null source) so it only ever touches that one specific row.
return new class extends Migration
{
    public function up(): void
    {
        $legacyDuplicate = WebChapter::where('title', 'Parts of Speech')
            ->whereNull('source_book_chapter_id')
            ->first();

        if ($legacyDuplicate) {
            $stillHasTrackedCopy = WebChapter::where('title', 'Parts of Speech')
                ->where('section_id', $legacyDuplicate->section_id)
                ->whereNotNull('source_book_chapter_id')
                ->exists();

            // Only delete the untracked one if a properly-tracked copy also
            // exists - never delete the only copy of something.
            if ($stillHasTrackedCopy) {
                WebLesson::where('web_chapter_id', $legacyDuplicate->id)->delete();
                $legacyDuplicate->delete();
            }
        }
    }

    public function down(): void
    {
        // Not reversible - this only ever deletes a confirmed duplicate.
    }
};
