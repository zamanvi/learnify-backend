<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A chapter within the website's ("Book 2") Section -> Chapter -> Lesson
 * structure. Independent from App\Models\Chapter (the existing app's own
 * chapter system) - see the web_chapters migration for why.
 */
class WebChapter extends Model
{
    protected $fillable = [
        'section_id',
        'title',
        'slug',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(WebLesson::class);
    }
}
