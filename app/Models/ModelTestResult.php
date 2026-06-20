<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelTestResult extends Model
{
    use HasFactory;
    protected $fillable = ['modeltest_id', 'user_id', 'type', 'total_q', 'r_ans', 'w_ans', 'total_mark', 'neg_mark', 'give_ans', 'not_give_ans'];

    public function modeltest()
    {
        return $this->belongsTo(ModelTestAll::class, 'modeltest_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
