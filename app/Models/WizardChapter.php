<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WizardChapter extends Model
{
    protected $fillable = ['title', 'subtitle', 'status', 'order_by'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function stories()
    {
        return $this->hasMany(WizardStory::class, 'chapter_id');
    }
}
