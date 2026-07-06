<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires re-specifying all enum values to add a new one
        DB::statement("ALTER TABLE chapters MODIFY COLUMN type ENUM('vocabulary','verb','grammar','both') NOT NULL DEFAULT 'both'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE chapters MODIFY COLUMN type ENUM('vocabulary','grammar','both') NOT NULL DEFAULT 'both'");
    }
};
