<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            // Persists whether invite_code is actually meant to be joinable,
            // instead of BattleController inferring it from invite_code being
            // non-null (which previously let a code be generated and shown
            // to the user even when max_participants had already locked the
            // battle to exactly one slot - a permanent dead end).
            $table->boolean('allow_code_join')->default(false)->after('max_participants');
        });
    }

    public function down(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            $table->dropColumn('allow_code_join');
        });
    }
};
