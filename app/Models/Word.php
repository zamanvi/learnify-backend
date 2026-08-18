<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    use HasFactory;
    protected $fillable = ['word', 'meaning', 'synonyms', 'antonyms', 'type', 'lesson_id', 'status', 'image'];

    protected $casts = ['status' => 'bool'];
    protected $appends = ['image_url'];

    /**
     * Relationship: Each word belongs to a lesson
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    // Full URL for the Picture-round quiz, same pattern as
    // User::getProfilePhotoAttribute() -> get_file(). Null when no image
    // has been uploaded for this word yet (falls back to a placeholder).
    public function getImageUrlAttribute()
    {
        if ($this->image != null) {
            return get_file($this->image, 'word');
        }
        return null;
    }
}
