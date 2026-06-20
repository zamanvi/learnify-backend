<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelSyllabus extends Model
{
    use HasFactory;
    protected $fillable = [
        'modeltest_id',
        'name',
        'description',
    ];
    public function modeltest()
    {
        return $this->belongsTo(ModelTestAll::class, 'modeltest_id');
    }
}
