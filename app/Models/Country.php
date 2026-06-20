<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $fillable = ['is_active', 'code', 'name'];
    public function division()
    {
        return $this->hasMany(Division::class);
    }
}
