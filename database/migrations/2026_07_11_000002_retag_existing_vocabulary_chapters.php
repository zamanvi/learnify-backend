<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Chapters 1-3 were created before the 'verb'/'vocabulary' distinction
     * existed and were mistagged as 'grammar' by default. Their lessons
     * were tagged 'both'. The Android app's LessonAdapter prefers a
     * lesson's own type over its parent chapter's, so both levels need
     * retagging for the category picker (and Verb1/2/3 labels) to work.
     */
    public function up(): void
    {
        DB::table('chapters')->where('id', 1)->update(['type' => 'vocabulary']); // Essential Vocabulary for All Learners
        DB::table('chapters')->where('id', 2)->update(['type' => 'verb']);       // Essential Verbs for All Learners
        DB::table('chapters')->where('id', 3)->update(['type' => 'vocabulary']); // Hsc english first paper words meaning

        DB::table('lessons')->whereIn('id', [2, 5])->update(['type' => 'verb']);       // Essential Verbs' 2 lessons
        DB::table('lessons')->whereIn('id', [1, 3, 4, 6, 7])->update(['type' => 'vocabulary']); // chapters 1 & 3's lessons
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('chapters')->whereIn('id', [1, 2, 3])->update(['type' => 'grammar']);
        DB::table('lessons')->whereIn('id', [1, 2, 3, 4, 5, 6, 7])->update(['type' => 'both']);
    }
};
