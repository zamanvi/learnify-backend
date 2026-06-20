<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScholarShip extends Model
{
    use HasFactory;
    protected $appends = ['image', 'sponsor_image'];
    protected $fillable = ['user_id', 'title', 'price', 'enroll_limit', 'winner_limit', 'date', 'time', 'slug', 'is_publish', 'short_description', 'description', 'image_path', 'sponsor', 'sponsor_image_path', 's_country', 's_division', 's_city', 's_upazila', 'pageview', 'status'];

    public function getImageAttribute()
    {
        return get_file($this->attributes['image_path']);
    }

    public function getSponsorImageAttribute()
    {
        return get_file($this->attributes['sponsor_image_path']);
    }

    public function enrollments()
    {
        return $this->hasMany(ScholarShipEnroll::class, 'scholar_ship_id');
    }
    public function results()
    {
        return $this->hasMany(ScholarShipResult::class, 'scholar_ship_id');
    }

    /**
     * Check if the scholarship is currently valid for result
     *
     * @return bool
     */
    public function isValidForResult()
    {
        return $this->is_publish && !$this->status;
    }
    /**
     * Check if the scholarship is currently valid for enrollment
     *
     * @return bool
     */
    public function isValidForEnrollment()
    {
        $scholarshipDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$this->date} {$this->time}");
        return !$this->is_publish && $this->status && $scholarshipDateTime->isFuture();
    }
    /**
     * Scope a query to only include scholarships that are valid for enrollment
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool $status
     * @param bool $is_publish
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeValidForEnrollment($query, $status, $is_publish)
    {
        $currentDateTime = Carbon::now();

        if ($status) {
            // Active scholarships: is_publish is false, status is true, and date/time is in the future
            return $query->where('status', $status)
                         ->where('is_publish', $is_publish)
                         ->whereRaw("STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i') > ?", [$currentDateTime]);
        } else {
            // Inactive scholarships: is_publish is true or date/time is in the past
            return $query->where(function ($q) use ($is_publish, $currentDateTime) {
                $q->where('is_publish', $is_publish)
                  ->orWhereRaw("STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i') <= ?", [$currentDateTime]);
            });
        }
    }

    public static function createStore($request): bool
    {
        if ($request->hasFile('image_path')) {
            $image_path = upload_file($request->image_path);
        } else {
            $image_path = '';
        }
        if ($request->hasFile('sponsor_image_path')) {
            $sponsor_image_path = upload_file($request->sponsor_image_path);
        } else {
            $sponsor_image_path = '';
        }
        $slug = $request->slug != null ? make_slug($request->slug) : make_slug($request->title);
        while (self::where('slug', $slug)->exists()) {
            $slug = set_increment_slug(ScholarShip::class, $slug);
        }
        $newEntry = self::create([
            'user_id' => auth()->user()->id,
            'title' => $request->title,
            'price' => $request->price,
            'enroll_limit' => $request->enroll_limit,
            'winner_limit' => $request->winner_limit,
            'date' => $request->date,
            'time' => $request->time,
            'slug' => $slug,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'image_path' => $image_path,
            'sponsor' => $request->sponsor,
            'sponsor_image_path' => $sponsor_image_path,
            's_country' => $request->s_country,
            's_division' => $request->s_division,
            's_city' => $request->s_city,
            's_upazila' => $request->s_upazila,
        ]);
        return $newEntry instanceof self;
    }

    public static function updateStore($request, $id): bool
    {
        $scholarShip = ScholarShip::find($id);
        if (!$scholarShip) {
            return false;
        }
        $image_path = $scholarShip->image_path;
        if ($request->hasFile('image_path')) {
            $image_path = upload_file($request->image_path);
        }
        $sponsor_image_path = $scholarShip->sponsor_image_path;
        if ($request->hasFile('sponsor_image_path')) {
            $sponsor_image_path = upload_file($request->sponsor_image_path);
        }

        $slug = $request->slug != null ? make_slug($request->slug) : make_slug($request->name) . '-' . get_random_number(10);
        if ($slug != $scholarShip->slug) {
            while (self::where('slug', $slug)->exists()) {
                $slug = set_increment_slug(Blog::class, $slug);
            }
        }
        return $scholarShip->update([
            'title' => $request->title,
            'price' => $request->price,
            'enroll_limit' => $request->enroll_limit,
            'winner_limit' => $request->winner_limit,
            'date' => $request->date,
            'time' => $request->time,
            'slug' => $slug,
            'short_description' => $request->short_description,
            'description' => $request->description,
            'image_path' => $image_path,
            'sponsor' => $request->sponsor,
            'sponsor_image_path' => $sponsor_image_path,
            's_country' => $request->s_country,
            's_division' => $request->s_division,
            's_city' => $request->s_city,
            's_upazila' => $request->s_upazila,
        ]);
    }
}
