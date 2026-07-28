<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLessonRoundProgress extends Model
{
    protected $table = 'user_lesson_round_progress';

    protected $fillable = [
        'user_id', 'lesson_id', 'round_number', 'status',
        'stars', 'best_score', 'best_total', 'hearts_lost_best_attempt',
        'attempts', 'last_attempt_at',
    ];

    protected $casts = [
        'last_attempt_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
