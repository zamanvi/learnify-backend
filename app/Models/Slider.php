<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $appends = ['image'];
    protected $fillable = ['type', 'short_description', 'image_path'];

    public function getImageAttribute()
    {
        return $this->image_path != null ? get_file($this->image_path, '') : empty_image('contest');
    }
}
