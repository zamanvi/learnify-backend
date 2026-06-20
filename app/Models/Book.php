<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = ['owner_id', 'title', 'slug', 'status', 'pageview', 'added_by'];

    public function chapters()
    {
        return $this->hasMany(BookChapter::class);
    }

    public static function createStore($request) : bool
    {
        $slug = $request->slug != null ? make_slug($request->slug) : make_slug($request->title);
        while (self::where('slug', $slug)->exists()) {
            $slug = set_increment_slug(Book::class, $slug);
        }
        $newEntry = self::create([
            'title' => $request->title,
            'slug' => $slug,
            'added_by' => auth()->user()->id
        ]);
        return $newEntry instanceof self;
    }
    public static function updateStore($request, $id) : bool
    {
        $book = self::find($id);
        if (!$book) {
            return false;
        }
        $slug = $request->slug != null ? make_slug($request->slug) : make_slug($request->title) . '-' . get_random_number(10);
        if ($slug != $book->slug) {
            while (self::where('slug', $slug)->exists()) {
                $slug = set_increment_slug(Book::class, $slug);
            }
        }
        $updateEntry = self::where('id', $id)->update([
            'title' => $request->title,
            'slug' => $slug,
        ]);
        return $updateEntry;
    }

}
