<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelQuestion extends Model
{
    use HasFactory;
    protected $fillable = [
        'modeltest_id',
        'name',
        'option1',
        'option2',
        'option3',
        'option4',
        'option5',
    ];
    public function modeltest()
    {
        return $this->belongsTo(ModelTestAll::class, 'modeltest_id');
    }
}
