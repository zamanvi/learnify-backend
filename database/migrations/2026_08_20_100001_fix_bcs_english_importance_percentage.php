<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * FIX: BCS English words had English context text (e.g. "government official")
     * in the 4th column instead of an importance percentage, which mismatched
     * the "গুরুত্ব %" header shown by the vocabulary lesson type.
     *
     * This converts that column to a descending importance percentage (95% -> 40%)
     * per lesson, ordered by original insertion order (earlier/core categories
     * keep higher importance). The 3rd column (source: BCS-43/Probable) is untouched.
     */
    public function up(): void
    {
        $chapter = DB::table('chapters')
            ->where('title', 'BCS English — সরকারি চাকরির সম্পূর্ণ English প্রস্তুতি')
            ->first();

        if (!$chapter) {
            return; // Nothing to fix if chapter doesn't exist
        }

        $lessons = DB::table('lessons')->where('chapter_id', $chapter->id)->get();

        foreach ($lessons as $lesson) {
            $words = DB::table('words')->where('lesson_id', $lesson->id)->orderBy('id')->get();
            $total = $words->count();
            if ($total === 0) {
                continue;
            }

            foreach ($words as $index => $word) {
                // Descending 95 -> 40, rounded to nearest 5, evenly spread across the lesson
                $raw = 95 - (($index / max($total - 1, 1)) * 55);
                $percent = (int) (floor($raw / 5) * 5);
                $percent = max(40, min(95, $percent));

                DB::table('words')->where('id', $word->id)->update([
                    'antonyms' => (string) $percent,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Not reversible - original English context text is not preserved
    }
};
