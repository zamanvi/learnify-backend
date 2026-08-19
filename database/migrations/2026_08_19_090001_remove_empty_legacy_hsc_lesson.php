<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Removes a leftover, empty legacy lesson under the "Hsc english first
     * paper words meaning" chapter (id=3): "Unite 1- Lesson - 1 (the
     * unforgettable history)". This lesson predates the 15-unit NCTB-aligned
     * vocabulary added in 2026_07_28_100001_add_hsc_english_first_paper_vocabulary
     * and has zero words attached (verified live in the app: "এই লেসনে এখনো
     * কোনো শব্দ যোগ করা হয়নি"). It sat above "Unit 1: People or Institutions
     * Making History" and confused the unit ordering. Safe to delete: no words
     * reference it, so nothing is lost.
     */
    public function up(): void
    {
        $chapterId = 3; // "Hsc english first paper words meaning"
        $legacyTitle = 'Unite 1- Lesson - 1 (the unforgettable history)';

        $lesson = DB::table('lessons')
            ->where('chapter_id', $chapterId)
            ->where('title', $legacyTitle)
            ->first();

        if ($lesson) {
            // Defensive: only delete words too if any ever got attached later.
            DB::table('words')->where('lesson_id', $lesson->id)->delete();
            DB::table('lessons')->where('id', $lesson->id)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $chapterId = 3;
        $legacyTitle = 'Unite 1- Lesson - 1 (the unforgettable history)';

        $exists = DB::table('lessons')
            ->where('chapter_id', $chapterId)
            ->where('title', $legacyTitle)
            ->exists();

        if (!$exists) {
            $now = now();
            DB::table('lessons')->insert([
                'title'      => $legacyTitle,
                'type'       => 'vocabulary',
                'chapter_id' => $chapterId,
                'status'     => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
};
