<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A lesson within a WebChapter, for the website's ("Book 2") Section ->
 * Chapter -> Lesson structure. Independent from App\Models\Lesson (the
 * existing app's own lesson system) - see the web_lessons migration for why.
 */
class WebLesson extends Model
{
    protected $fillable = [
        'web_chapter_id',
        'title',
        'slug',
        'content',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(WebChapter::class, 'web_chapter_id');
    }

    public static function createStore($request): bool
    {
        $slug = $request->slug != null ? make_slug($request->slug) : make_slug($request->title);
        while (self::where('slug', $slug)->exists()) {
            $slug = set_increment_slug(self::class, $slug);
        }
        $newEntry = self::create([
            'web_chapter_id' => $request->web_chapter_id,
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);
        return $newEntry instanceof self;
    }

    public static function updateStore($request, $id): bool
    {
        $lesson = self::find($id);
        if (!$lesson) {
            return false;
        }
        $slug = $request->slug != null ? make_slug($request->slug) : $lesson->slug;
        if ($slug != $lesson->slug) {
            while (self::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                $slug = set_increment_slug(self::class, $slug);
            }
        }
        return self::where('id', $id)->update([
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->content,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);
    }
}
