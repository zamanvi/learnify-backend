<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('words', 'image')) return;

        Schema::table('words', function (Blueprint $table) {
            // Filename only (mirrors users.profile_photo_path) - resolved to
            // a full URL via Word::getImageUrlAttribute() / get_file().
            // Nullable: most existing words have no picture yet, and the
            // Round 3 (Picture) quiz round only draws from words that do.
            $table->string('image')->nullable()->after('antonyms');
        });
    }

    public function down(): void
    {
        Schema::table('words', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
