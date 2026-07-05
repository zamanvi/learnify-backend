<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'lipto_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedBigInteger('lipto_balance')->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'lipto_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('lipto_balance');
            });
        }
    }
};
