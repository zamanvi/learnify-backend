<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookChapter extends Model
{
    use HasFactory;
    protected $fillable = ['book_id', 'title', 'type', 'slug', 'status', 'pageview', 'added_by'];

    /**
     * Get the book that owns the BookChapter
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }

    public function items()
    {
        return $this->hasMany(BookItem::class, 'chapter_id');
    }

    public static function createStore($request) : bool
    {
        $slug = $request->slug != null ? make_slug($request->slug) : make_slug($request->title);
        while (self::where('slug', $slug)->exists()) {
            $slug = set_increment_slug(BookChapter::class, $slug);
        }
        $newEntry = self::create([
            'book_id' => $request->book_id,
            'title' => $request->title,
            'type' => $request->type,
            'slug' => $slug,
            'added_by' => auth()->user()->id
        ]);
        return $newEntry instanceof self;
    }
    public static function updateStore($request, $id) : bool
    {
        $bookChapter = self::find($id);
        if (!$bookChapter) {
            return false;
        }
        $slug = $request->slug != null ? make_slug($request->slug) : make_slug($request->title) . '-' . get_random_number(10);
        if ($slug != $bookChapter->slug) {
            while (self::where('slug', $slug)->exists()) {
                $slug = set_increment_slug(BookChapter::class, $slug);
            }
        }
        $updateEntry = self::where('id', $id)->update([
            'title' => $request->title,
            'slug' => $slug,
            'type' => $request->type
        ]);
        return $updateEntry;
    }

}
