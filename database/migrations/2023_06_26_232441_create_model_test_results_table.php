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
        Schema::create('model_test_results', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('modeltest_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->string('type');
            $table->string('total_q');
            $table->string('r_ans');
            $table->string('w_ans');
            $table->string('total_mark');
            $table->string('neg_mark');
            $table->string('give_ans');
            $table->string('not_give_ans');
            $table->timestamps();
            $table
                ->foreign('modeltest_id')
                ->references('id')
                ->on('model_test_alls')
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
        Schema::dropIfExists('model_test_results');
    }
};
