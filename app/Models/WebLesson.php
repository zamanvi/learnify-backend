<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A lesson within a WebChapter, for the website's ("Book 2") Section ->
 * Chapter -> Lesson structure. Independent from App\Models\Lesson (the
 * existing app's own lesson system) - see the web_lessons migration for why.
 */
class WebLesson extends Model
{
    protected $fillable = [
        'web_chapter_id',
        'title',
        'slug',
        'content',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(WebChapter::class, 'web_chapter_id');
    }
}
