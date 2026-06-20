<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    protected $fillable = ['division_id', 'name'];
    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
    public function upazila()
    {
        return $this->hasMany(Upazila::class);
    }
}
