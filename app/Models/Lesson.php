<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'type', 'chapter_id', 'status', 'is_premium', 'pattern'];
    protected $casts = ['status' => 'bool', 'is_premium' => 'bool'];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * Generate hint text dynamically based on lesson pattern.
     * This ensures the hint matches actual available fields in the pattern.
     */
    public function getHintTextAttribute(): string
    {
        return match($this->pattern) {
            'standard' => 'শব্দ | অর্থ',                    // Word | Meaning
            'exam' => 'অর্থ | পরীক্ষা %',                   // Meaning | Exam %
            'medical' => 'অর্থ | উৎস / %',                  // Meaning | Source / %
            default => 'শব্দ | অর্থ'                        // Default fallback
        };
    }
}
