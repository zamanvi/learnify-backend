<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'type', 'chapter_id', 'status', 'is_premium'];
    protected $casts = ['status' => 'bool', 'is_premium' => 'bool'];

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }
}
