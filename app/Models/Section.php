<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    // NOTE: no chapters() relationship yet. The existing `chapters` table
    // (live app data, since 2025-12-03) has no section_id column - linking
    // Section -> Chapter needs a separate, careful ALTER migration that adds
    // a nullable section_id to the existing table, done as its own follow-up
    // with the live app's current Chapter/Lesson behavior verified unchanged.
}
