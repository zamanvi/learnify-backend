<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-lesson, admin-typed column headings for the Word Create/Edit form
     * and the app's word-list header row. All 4 are nullable and purely
     * additive: existing lessons (all of them, right now) have every one of
     * these as null, which is deliberate - Lesson::column_labels() treats
     * "all 4 null" as "this lesson hasn't opted into custom labels yet" and
     * falls back to the exact same type/pattern-based labels it already
     * shows today. Nothing about existing lessons' admin form or app display
     * changes because of this migration by itself.
     */
    public function up(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('col1_label')->nullable()->after('pattern');
            $table->string('col2_label')->nullable()->after('col1_label');
            $table->string('col3_label')->nullable()->after('col2_label');
            $table->string('col4_label')->nullable()->after('col3_label');
        });
    }

    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['col1_label', 'col2_label', 'col3_label', 'col4_label']);
        });
    }
};
