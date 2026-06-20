<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelTestAll extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'class_id',
        'subject',
        'type',
        'duration',
    ];
    public function allclass()
    {
        return $this->belongsTo(AllClass::class, 'class_id');
    }
    public function modelsyllabus()
    {
        return $this->hasMany(ModelSyllabus::class);
    }
    public function modelquestion()
    {
        return $this->hasMany(ModelQuestion::class);
    }
    public function modeltestresult()
    {
        return $this->hasMany(ModelTestResult::class);
    }
}
