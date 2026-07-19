<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WizardStory extends Model
{
    protected $fillable = [
        'chapter_id', 'hook_title', 'meta',
        'english_paragraphs', 'bangla_title', 'bangla_paragraphs',
        'grammar_notes', 'vocabulary', 'status', 'order_by',
    ];

    protected $casts = [
        'english_paragraphs' => 'array',
        'bangla_paragraphs' => 'array',
        'grammar_notes' => 'array',
        'vocabulary' => 'array',
        'status' => 'boolean',
    ];

    public function chapter()
    {
        return $this->belongsTo(WizardChapter::class, 'chapter_id');
    }
}
