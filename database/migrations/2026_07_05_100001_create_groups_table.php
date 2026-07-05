<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('groups')) {
            Schema::create('groups', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('created_by');
                $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
                $table->string('name', 100);
                $table->string('code', 8)->unique();   // invite code e.g. "AB12CD34"
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index('code');
            });
        }

        if (!Schema::hasTable('group_members')) {
            Schema::create('group_members', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('group_id');
                $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
                $table->unsignedInteger('user_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['group_id', 'user_id']);
                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('group_members');
        Schema::dropIfExists('groups');
    }
};
