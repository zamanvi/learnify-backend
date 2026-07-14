<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('wizard_chapters')) {
        Schema::create('wizard_chapters', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('order_by')->default(0);
            $table->timestamps();
        });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('wizard_chapters');
    }
};
