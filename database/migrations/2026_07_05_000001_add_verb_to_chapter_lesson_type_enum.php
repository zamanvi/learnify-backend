<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE chapters MODIFY COLUMN type ENUM('vocabulary','grammar','verb','both') DEFAULT 'both'");
        DB::statement("ALTER TABLE lessons  MODIFY COLUMN type ENUM('vocabulary','grammar','verb','both') DEFAULT 'both'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE chapters MODIFY COLUMN type ENUM('vocabulary','grammar','both') DEFAULT 'both'");
        DB::statement("ALTER TABLE lessons  MODIFY COLUMN type ENUM('vocabulary','grammar','both') DEFAULT 'both'");
    }
};
