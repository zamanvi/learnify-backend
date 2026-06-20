<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('support_replays', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('support_id')->unsigned();
            $table->string('sender_id');
            $table->string('receiver_id');
            $table->longText('message');
            $table->timestamps();
            $table
                ->foreign('support_id')
                ->references('id')
                ->on('supports')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_replays');
    }
};
