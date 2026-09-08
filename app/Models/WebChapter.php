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

    /**
     * Copies a BookChapter (+ its published BookItems) into a new
     * WebChapter (+ WebLessons) under the given Section. Shared by
     * WebSectionController (single + bulk copy tools) and any one-off
     * data-fix migration that needs the exact same copy semantics, so
     * both stay in sync.
     *
     * Copies the source's own slug (chapter and each item) as-is rather
     * than regenerating from title - many BookChapter/BookItem slugs are
     * hand-shortened/customized and no longer match make_slug(title),
     * and the website's URLs (/book/{slug}) need to stay stable for SEO.
     * Only falls back to an incremented slug if that exact slug is
     * already taken by an unrelated WebChapter/WebLesson (tracked via
     * source_book_chapter_id/source_book_item_id, not by title, so a
     * manually-renamed copy still counts as "the same one").
     *
     * Only copies published (status=true) items, so drafts on the app
     * side don't leak onto the live website. Strictly read-only against
     * Book/BookChapter/BookItem - never writes to them.
     */
    public static function copyFromBookChapter(BookChapter $bookChapter, int $sectionId): self
    {
        $slug = $bookChapter->slug;
        while (
            self::where('slug', $slug)
                ->where(fn ($q) => $q->whereNull('source_book_chapter_id')->orWhere('source_book_chapter_id', '!=', $bookChapter->id))
                ->exists()
        ) {
            $slug = set_increment_slug(self::class, $slug);
        }

        $webChapter = self::create([
            'section_id' => $sectionId,
            'source_book_chapter_id' => $bookChapter->id,
            'title' => $bookChapter->title,
            'slug' => $slug,
            'description' => null,
            'order' => 0,
            'is_active' => true,
        ]);

        $publishedItems = $bookChapter->items()->where('status', true)->get();
        foreach ($publishedItems as $item) {
            $lessonSlug = $item->slug;
            while (
                WebLesson::where('slug', $lessonSlug)
                    ->where(fn ($q) => $q->whereNull('source_book_item_id')->orWhere('source_book_item_id', '!=', $item->id))
                    ->exists()
            ) {
                $lessonSlug = set_increment_slug(WebLesson::class, $lessonSlug);
            }
            WebLesson::create([
                'web_chapter_id' => $webChapter->id,
                'source_book_item_id' => $item->id,
                'title' => $item->title,
                'slug' => $lessonSlug,
                'content' => $item->details,
                'short_details' => $item->short_details,
                'link' => $item->link,
                'keyword' => $item->keyword,
                'order' => 0,
                'is_active' => true,
            ]);
        }

        return $webChapter;
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
