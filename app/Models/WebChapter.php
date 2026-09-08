<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A chapter within the website's ("Book 2") Section -> Chapter -> Lesson
 * structure. Independent from App\Models\Chapter (the existing app's own
 * chapter system) - see the web_chapters migration for why.
 */
class WebChapter extends Model
{
    protected $fillable = [
        'section_id',
        'source_book_chapter_id',
        'title',
        'slug',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(WebLesson::class);
    }

    public static function createStore($request): bool
    {
        $slug = $request->slug != null ? make_slug($request->slug) : make_slug($request->title);
        while (self::where('slug', $slug)->exists()) {
            $slug = set_increment_slug(self::class, $slug);
        }
        $newEntry = self::create([
            'section_id' => $request->section_id,
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);
        return $newEntry instanceof self;
    }

    public static function updateStore($request, $id): bool
    {
        $chapter = self::find($id);
        if (!$chapter) {
            return false;
        }
        $slug = $request->slug != null ? make_slug($request->slug) : $chapter->slug;
        if ($slug != $chapter->slug) {
            while (self::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = set_increment_slug(self::class, $slug);
            }
        }
        return self::where('id', $id)->update([
            'title' => $request->title,
            'slug' => $slug,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);
    }
}
