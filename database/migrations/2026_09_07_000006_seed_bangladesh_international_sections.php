<?php

use App\Models\Section;
use Illuminate\Database\Migrations\Migration;

// Data migration, not just structure - the deploy command only runs
// `php artisan migrate --force` (see Dockerfile CMD), never `db:seed`, so
// SectionsSeeder alone would never actually run in production. Using
// updateOrCreate keeps this idempotent/safe to re-run.
return new class extends Migration
{
    public function up(): void
    {
        Section::updateOrCreate(
            ['slug' => 'bangladesh'],
            [
                'name' => 'Bangladesh',
                'description' => 'Bengali language learning content for Bangladesh region',
                'order' => 1,
                'is_active' => true,
            ]
        );

        Section::updateOrCreate(
            ['slug' => 'international'],
            [
                'name' => 'International',
                'description' => 'English language learning content for international audience',
                'order' => 2,
                'is_active' => true,
            ]
        );
    }

    public function down(): void
    {
        Section::whereIn('slug', ['bangladesh', 'international'])->delete();
    }
};
