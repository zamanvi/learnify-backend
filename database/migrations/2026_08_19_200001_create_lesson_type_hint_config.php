<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Create lesson type hint configuration table
     * This allows admins to configure column headers for different lesson types
     */
    public function up(): void
    {
        Schema::create('lesson_type_hint_configs', function (Blueprint $table) {
            $table->id();
            $table->string('type_name')->unique(); // 'vocabulary', 'verb', etc.
            $table->string('display_title'); // Display name for UI
            $table->string('column_1_hint')->default('শব্দ'); // Column 1 header
            $table->string('column_2_hint')->default('অর্থ'); // Column 2 header
            $table->string('column_3_hint'); // Column 3 header - varies by type
            $table->string('column_4_hint'); // Column 4 header - varies by type
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Insert default configurations for Vocabulary and Verb
        DB::table('lesson_type_hint_configs')->insert([
            [
                'type_name' => 'vocabulary',
                'display_title' => 'Vocabulary',
                'column_1_hint' => 'শব্দ',
                'column_2_hint' => 'অর্থ',
                'column_3_hint' => 'উৎস / বিষয়',
                'column_4_hint' => 'গুরুত্ব %',
                'description' => 'Vocabulary lessons with source and importance percentage',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_name' => 'verb',
                'display_title' => 'Verb',
                'column_1_hint' => 'Verb 1',
                'column_2_hint' => 'অর্থ',
                'column_3_hint' => 'Verb 2',
                'column_4_hint' => 'Verb 3',
                'description' => 'Verb conjugation lessons',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_type_hint_configs');
    }
};
