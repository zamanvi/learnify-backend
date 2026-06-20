<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shahid extends Model
{
    use HasFactory;
    protected $appends = ['thumbnail', 'gallery'];
    protected $fillable = ['name', 'address', 'description', 'thumbnail_path', 'gallery_path', 'video_link', 'pageview', 'status', 'slug', 'u_id'];

    public function getThumbnailAttribute()
    {
        if ($this->thumbnail_path != null) {
            $computedPhoto = get_file($this->thumbnail_path, 'user');
            return $computedPhoto;
        } else {
            $defaultPhoto = empty_image('user');
            return $defaultPhoto;
        }
    }
    public function getGalleryAttribute()
    {
        $galleryPaths = json_decode($this->gallery_path, true);

        if (!empty($galleryPaths)) {
            $computedGallery = [];
            foreach ($galleryPaths as $gallery_path) {
                $computedGallery[] = get_file($gallery_path, 'user');
            }
            return $computedGallery;
        }
    }

    public static function createStore($request): bool
    {
        if ($request->hasFile('thumbnail_path')) {
            $thumbnail_path = upload_file($request->file('thumbnail_path'));
        } else {
            $thumbnail_path = '';
        }
        $gallery_paths = [];
        if ($request->hasFile('gallery_path')) {
            foreach ($request->file('gallery_path') as $file) {
                $gallery_paths[] = upload_file($file);
            }
        }
        $gallery_paths = !empty($gallery_paths) ? json_encode($gallery_paths) : '';
        $slug = $request->slug != null ? make_slug($request->slug) : make_slug($request->name);
        while (self::where('slug', $slug)->exists()) {
            $slug = set_increment_slug(Shahid::class, $slug);
        }
        $newEntry = self::create([
            'name' => $request->name,
            'address' => $request->address,
            'description' => $request->description,
            'thumbnail_path' => $thumbnail_path,
            'gallery_path' => $gallery_paths,
            'video_link' => $request->video_link,
            'slug' => $slug,
            'u_id' => auth()->user()->id,
        ]);
        return $newEntry instanceof self;
    }

    public static function updateStore($request, $id): bool
    {
        $shahid = self::findOrFail($id);

        if ($request->hasFile('thumbnail_path')) {
            $shahid->thumbnail_path = upload_file($request->file('thumbnail_path'));
        }

        $gallery_paths = json_decode($shahid->gallery_path, true) ?? [];
        if ($request->hasFile('gallery_path')) {
            foreach ($request->file('gallery_path') as $file) {
                $gallery_paths[] = upload_file($file);
            }
            // dd($gallery_paths);
        }
        $shahid->gallery_path = !empty($gallery_paths) ? json_encode($gallery_paths) : '';

        // Update the slug
        $slug = $request->slug != null ? make_slug($request->slug) : make_slug($request->name);
        while (self::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            $slug = set_increment_slug(Shahid::class, $slug);
        }
        $shahid->slug = $slug;

        // Update other fields
        $shahid->name = $request->name;
        $shahid->address = $request->address;
        $shahid->description = $request->description;
        $shahid->video_link = $request->video_link;
        $shahid->u_id = auth()->user()->id;

        // Save the updated entry
        return $shahid->save();
    }
}
