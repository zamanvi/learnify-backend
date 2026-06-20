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
        Schema::create('contest_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('contest_id');
            $table->longText('name');
            $table->longText('option1');
            $table->longText('option2');
            $table->longText('option3');
            $table->longText('option4');
            $table->longText('option5');
            $table->timestamps();
            $table
                ->foreign('contest_id')
                ->references('id')
                ->on('contests')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contest_questions');
    }
};
