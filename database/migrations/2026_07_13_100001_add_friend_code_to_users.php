<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Adds a short, friendly, user-facing ID for friend-to-friend Lipto
    // transfer/sharing - separate from the existing redrose_id (which has an
    // inconsistent format across users and isn't meant to be user-facing).
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('friend_code', 6)->nullable()->unique()->after('redrose_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('friend_code');
        });
    }
};
