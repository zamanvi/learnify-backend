<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The `migrations` table lost its record of this one having already
        // run (likely from a lossy DB restore at some point — same pattern
        // as the missing-PRIMARY-KEY incident on the book tables), while the
        // underlying `all_classes` table itself survived intact. Laravel was
        // therefore retrying this on every deploy, failing on the duplicate
        // table, and aborting the whole migrate run — silently blocking
        // every migration after this one (including friend_code, is_premium,
        // and this chapter-title-typo fix) for potentially a long time.
        if (!Schema::hasTable('all_classes')) {
            Schema::create('all_classes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('all_classes');
    }
};
