<?php

use App\Models\BookItem;
use App\Models\WebLesson;
use Illuminate\Database\Migrations\Migration;

// Backfills short_details/link/keyword (added in the previous migration)
// for WebLessons that were already copied before those columns existed -
// matched via source_book_item_id, read-only against BookItem.
return new class extends Migration
{
    public function up(): void
    {
        WebLesson::whereNotNull('source_book_item_id')->chunkById(50, function ($lessons) {
            $bookItemIds = $lessons->pluck('source_book_item_id')->all();
            $bookItems = BookItem::whereIn('id', $bookItemIds)->get()->keyBy('id');

            foreach ($lessons as $lesson) {
                $item = $bookItems->get($lesson->source_book_item_id);
                if ($item) {
                    $lesson->update([
                        'short_details' => $item->short_details,
                        'link' => $item->link,
                        'keyword' => $item->keyword,
                    ]);
                }
            }
        });
    }

    public function down(): void
    {
        // Not reversible - re-run is the recovery path if needed.
    }
};
