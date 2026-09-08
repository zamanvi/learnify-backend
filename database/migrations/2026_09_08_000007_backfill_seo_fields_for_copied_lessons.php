<?php

use App\Models\BookItem;
use App\Models\WebLesson;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Backfills short_details/link/keyword (added in the previous migration)
// for WebLessons that were already copied before those columns existed -
// matched via source_book_item_id, read-only against BookItem. Only ~56
// rows exist, so this loads them all at once (no chunking - the earlier
// version of this migration used chunkById()+update() inside the callback,
// a known-risky Laravel combination, and caused a brief production outage;
// this simpler version avoids that pattern entirely) and writes with plain
// DB::table() updates instead of touching Eloquent model events per row.
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
