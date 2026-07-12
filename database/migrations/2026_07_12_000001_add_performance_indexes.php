<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adds indexes for columns that are queried/sorted on every request
     * of a hot endpoint but were never indexed. Purely additive - no
     * column or data changes, so existing queries/output are unaffected.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('points');
        });

        Schema::table('notices', function (Blueprint $table) {
            $table->index(['app', 'created_at']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['points']);
        });

        Schema::table('notices', function (Blueprint $table) {
            $table->dropIndex(['app', 'created_at']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex(['key']);
        });
    }
};
