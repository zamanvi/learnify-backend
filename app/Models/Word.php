<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    use HasFactory;
    protected $fillable = ['word', 'meaning', 'synonyms', 'antonyms', 'type', 'lesson_id', 'status'];

    protected $casts = ['status' => 'bool'];

    /**
     * Relationship: Each word belongs to a lesson
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
