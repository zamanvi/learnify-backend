<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BattleParticipant extends Model
{
    protected $fillable = [
        'battle_id', 'user_id', 'is_creator',
        'score', 'total', 'time_sec', 'lives_remaining',
        'status', 'joined_at', 'finished_at',
    ];

    protected $casts = [
        'is_creator' => 'boolean',
        'joined_at'  => 'datetime',
        'finished_at'=> 'datetime',
    ];

    public function battle()
    {
        return $this->belongsTo(Battle::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
