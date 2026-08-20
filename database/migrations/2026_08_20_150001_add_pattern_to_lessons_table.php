<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds 'pattern' column to lessons table to support Lesson-level
     * content structure patterns (e.g., 'standard', 'exam', 'medical').
     *
     * Backward compatible:
     * - Column is nullable, default NULL
     * - Existing lessons continue to work unchanged
     * - No existing data is modified
     * - API returns default pattern when NULL
     */
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            // Add pattern column after 'type' column
            // Nullable to maintain backward compatibility
            $table->string('pattern')->nullable()->default(null)->after('type')->comment('Lesson content pattern: standard, exam, medical, etc.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn('pattern');
        });
    }
};
