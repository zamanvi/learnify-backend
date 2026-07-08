<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE lessons MODIFY COLUMN type ENUM('vocabulary','verb','grammar','both','american_british','exam_vocab_appeared','exam_vocab_upcoming') DEFAULT 'both'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE lessons MODIFY COLUMN type ENUM('vocabulary','verb','grammar','both') DEFAULT 'both'");
    }
};
