<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Battle extends Model
{
    protected $fillable = [
        'challenger_id', 'opponent_id', 'lesson_id', 'status',
        'challenger_score', 'challenger_total', 'challenger_time_sec',
        'opponent_score', 'opponent_total', 'opponent_time_sec',
        'winner_id',
        'mode', 'question_count', 'lives', 'question_word_ids',
        'invite_code', 'max_participants', 'allow_code_join',
    ];

    protected $casts = [
        'question_word_ids' => 'array',
        'allow_code_join'   => 'boolean',
    ];

    public function challenger()
    {
        return $this->belongsTo(User::class, 'challenger_id');
    }

    public function opponent()
    {
        return $this->belongsTo(User::class, 'opponent_id');
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function battle_participants()
    {
        return $this->hasMany(BattleParticipant::class);
    }
}
