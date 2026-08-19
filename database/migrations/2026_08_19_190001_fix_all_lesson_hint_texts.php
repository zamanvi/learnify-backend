<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * FIX: Ensure all vocabulary lessons have correct lesson_type
     * This ensures WordActivity.java displays correct hint texts:
     * - vocabulary type → "উৎস / বিষয়" | "গুরুত্ব %"
     * - Other types → their specific headers
     */
    public function up(): void
    {
        // Get all vocabulary-related chapters
        $vocabChapters = DB::table('chapters')
            ->where('type', 'vocabulary')
            ->get();

        foreach ($vocabChapters as $chapter) {
            // Update all lessons in vocabulary chapters to type='vocabulary'
            DB::table('lessons')
                ->where('chapter_id', $chapter->id)
                ->update(['type' => 'vocabulary']);
        }

        // Also handle any lessons that might be vocabulary but in other chapters
        // For example, SSC, JSC, BCS, Bank chapters that should all use vocabulary type
        $chaptersToFix = DB::table('chapters')
            ->whereRaw("LOWER(title) LIKE '%english%' OR LOWER(title) LIKE '%vocab%' OR LOWER(title) LIKE '%bank%' OR LOWER(title) LIKE '%বাংক%' OR LOWER(title) LIKE '%ব্যাংক%'")
            ->where('type', 'vocabulary')
            ->get();

        foreach ($chaptersToFix as $chapter) {
            DB::table('lessons')
                ->where('chapter_id', $chapter->id)
                ->where('type', '!=', 'vocabulary')
                ->update(['type' => 'vocabulary']);
        }

        // Verify the fix
        $totalFixed = DB::table('lessons')
            ->whereIn('chapter_id', function ($query) {
                $query->select('id')
                    ->from('chapters')
                    ->where('type', 'vocabulary');
            })
            ->where('type', 'vocabulary')
            ->count();

        \Log::info("Fixed lesson types: {$totalFixed} vocabulary lessons now have correct type");
    }

    public function down(): void
    {
        // Rollback is not necessary for type field fixes
    }
};
