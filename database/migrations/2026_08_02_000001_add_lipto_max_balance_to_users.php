<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'lipto_max_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('lipto_max_balance')->default(0)->after('lipto_balance');
            });
        }

        // Seed from today's balance so existing users don't look like they
        // just got demoted the moment this column starts being read.
        DB::table('users')->update(['lipto_max_balance' => DB::raw('lipto_balance')]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'lipto_max_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('lipto_max_balance');
            });
        }
    }
};
