<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('lipto_transactions')) return;
        Schema::create('lipto_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->bigInteger('amount');               // positive=earn, negative=spend
            $table->string('type', 30);                // earn | spend | transfer_in | transfer_out
            $table->string('source', 50)->nullable();  // quiz | reward | purchase | transfer | admin
            $table->string('description')->nullable();
            $table->unsignedBigInteger('balance_after')->default(0);
            $table->unsignedBigInteger('related_user_id')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lipto_transactions');
    }
};
