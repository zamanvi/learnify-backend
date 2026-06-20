<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;
    protected $fillable = ['contest_id', 'user_id', 'total_q', 'r_ans', 'w_ans', 'total_mark', 'neg_mark', 'give_ans', 'not_give_ans', 'is_in_com'];

    protected $casts = [
        'total_mark' => 'float', // or 'decimal:2'
    ];

    public function contest()
    {
        return $this->belongsTo(Contest::class, 'contest_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
