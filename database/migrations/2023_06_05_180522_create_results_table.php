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
        Schema::create('results', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('contest_id');
            $table->integer('user_id')->unsigned();
            $table->string('total_q')->nullable();
            $table->string('r_ans')->nullable();
            $table->string('w_ans')->nullable();
            $table->string('total_mark')->nullable();
            $table->string('neg_mark')->nullable();
            $table->string('give_ans')->nullable();
            $table->string('not_give_ans')->nullable();
            $table->boolean('is_in_com')->default(true);
            $table->timestamps();
            $table
                ->foreign('contest_id')
                ->references('id')
                ->on('contests')
                ->onDelete('cascade');
            $table
                ->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
