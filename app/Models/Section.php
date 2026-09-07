<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Website ("Book 2") chapters in this section. Deliberately WebChapter,
     * not App\Models\Chapter - the existing app's chapter system is a
     * separate, independent structure. See web_chapters migration.
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(WebChapter::class);
    }
}
