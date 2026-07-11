<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE words MODIFY COLUMN type ENUM('vocabulary', 'grammar', 'both', 'verb') DEFAULT 'both'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE words MODIFY COLUMN type ENUM('vocabulary', 'grammar', 'both') DEFAULT 'both'");
    }
};
