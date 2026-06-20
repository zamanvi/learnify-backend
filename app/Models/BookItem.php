<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookItem extends Model
{
    use HasFactory;
    protected $fillable = ['chapter_id', 'title', 'sub_title', 'type', 'short_details', 'details', 'audio_text', 'link', 'keyword', 'slug', 'status', 'pageview', 'added_by'];

    /**
     * Get the chapter that owns the BookItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chapter()
    {
        return $this->belongsTo(BookChapter::class, 'chapter_id');
    }

    private static function increment_slug($slug)
    {
        $max = self::where('slug', 'LIKE', "{$slug}%")->latest('id')->value('slug');
        if ($max) {
            $parts = explode('-', $max);
            $number = intval(end($parts));
            $slug = $slug . '-' . ($number + 1);
        } else {
            $slug = $slug . '-1';
        }
        return $slug . '-' . get_random_number(10);
    }

    public static function createStore($request) : bool
    {
        $slug = $request->slug != null ? make_slug($request->slug) : make_slug($request->title);
        while (self::where('slug', $slug)->exists()) {
            $slug = set_increment_slug(BookItem::class, $slug);
        }
        $newEntry = self::create([
            'chapter_id' => $request->id,
            'title' => $request->title,
            'sub_title' => $request->sub_title,
            'type' => $request->type,
            'short_details' => $request->short_details,
            'details' => $request->details,
            'audio_text' => $request->audio_text,
            'link' => $request->link,
            'keyword' => $request->keyword,
            'slug' => $slug,
            'added_by' => auth()->user()->id
        ]);
        return $newEntry instanceof self;
    }
    public static function updateStore($request, $id) : bool
    {
        $bookItem = self::find($id);
        if (!$bookItem) {
            return false;
        }
        $slug = $request->slug != null ? make_slug($request->slug) : make_slug($request->title) . '-' . get_random_number(10);
        if ($slug != $bookItem->slug) {
            while (self::where('slug', $slug)->exists()) {
                $slug = set_increment_slug(BookItem::class, $slug);
            }
        }
        $updateEntry = self::where('id', $id)->update([
            'title' => $request->title,
            'sub_title' => $request->sub_title,
            'type' => $request->type,
            'short_details' => $request->short_details,
            'details' => $request->details,
            'audio_text' => $request->audio_text,
            'link' => $request->link,
            'keyword' => $request->keyword,
            'slug' => $slug
        ]);
        return $updateEntry;
    }

}
