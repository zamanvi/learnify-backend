<?php

use App\Models\BookItem;
use App\Models\WebLesson;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Retries the short_details/link/keyword backfill for the ~56 already-copied
// lessons, now that web_lessons.short_details/link/keyword are (long)text
// (see 2026_09_08_000008_widen_short_details_to_text) instead of the
// varchar(255) that caused this to crash the app earlier. Idempotent - safe
// to run again even for rows a partial earlier attempt already updated.
// Read-only against BookItem throughout.
return new class extends Migration
{
    public function up(): void
    {
        $lessons = WebLesson::whereNotNull('source_book_item_id')->get(['id', 'source_book_item_id']);
        if ($lessons->isEmpty()) {
            return;
        }

        $bookItemIds = $lessons->pluck('source_book_item_id')->unique()->all();
        $bookItems = BookItem::whereIn('id', $bookItemIds)
            ->get(['id', 'short_details', 'link', 'keyword'])
            ->keyBy('id');

        foreach ($lessons as $lesson) {
            $item = $bookItems->get($lesson->source_book_item_id);
            if ($item) {
                DB::table('web_lessons')->where('id', $lesson->id)->update([
                    'short_details' => $item->short_details,
                    'link' => $item->link,
                    'keyword' => $item->keyword,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Not reversible - re-run is the recovery path if needed.
    }
};
