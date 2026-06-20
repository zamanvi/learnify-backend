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
        Schema::create('model_syllabi', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('modeltest_id')->unsigned();
            $table->string('name');
            $table->longText('description');
            $table->timestamps();
            $table
                ->foreign('modeltest_id')
                ->references('id')
                ->on('model_test_alls')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_syllabi');
    }
};
