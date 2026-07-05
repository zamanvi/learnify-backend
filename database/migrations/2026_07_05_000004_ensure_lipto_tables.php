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

        if (!Schema::hasTable('lipto_transactions')) {
            Schema::create('lipto_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->bigInteger('amount');
                $table->string('type', 30);
                $table->string('source', 50)->nullable();
                $table->string('description')->nullable();
                $table->unsignedBigInteger('balance_after')->default(0);
                $table->unsignedBigInteger('related_user_id')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lipto_transactions');
        if (Schema::hasColumn('users', 'lipto_balance')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('lipto_balance');
            });
        }
    }
};
