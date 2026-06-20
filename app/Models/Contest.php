<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Contest extends Model
{
    use HasFactory;
    protected $appends = ['image', 'is_exam'];
    protected $fillable = ['name', 'date', 'time', 'status', 'price', 'duration', 'image_path','syllabus_title', 'description', 'slug', 'u_id'];

    public function getImageAttribute()
    {
        return $this->image_path != null ? get_file($this->image_path, 'contest') : empty_image('contest');
    }
    public function results()
    {
        return $this->hasMany(Result::class, 'contest_id');
    }

    public function getIsExamAttribute()
    {
        if (Auth::check()) {
            return $this->results()->where('user_id', Auth::id())->exists();
        }
        return false;
    }

/**
     * Check if the contest is currently active
     *
     * @return bool
     */
    public function isActive()
    {
        $contestDateTime = Carbon::createFromFormat('Y-m-d H:i', "{$this->date} {$this->time}");
        $contestEndDateTime = $contestDateTime->copy()->addSeconds($this->duration);
        return $this->status && $contestEndDateTime->isFuture();
    }

    /**
     * Scope a query to only include contests that are active
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query, $status = true)
    {
        $currentDateTime = Carbon::now();
        if ($status) {
            return $query->where('status', true)
                         ->whereRaw("STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i') + INTERVAL duration MINUTE > ?", [$currentDateTime]);
        } else {
            return $query->where(function ($q) use ($currentDateTime) {
                $q->where('status', false)
                  ->orWhereRaw("STR_TO_DATE(CONCAT(date, ' ', time), '%Y-%m-%d %H:%i') + INTERVAL duration MINUTE <= ?", [$currentDateTime]);
            });
        }
    }

    public static function createStore($request): bool
    {
        $image = $request->hasFile('image_path') ? upload_file($request->file('image_path')) : '';
        $slug = $request->slug != null ? make_slug($request->slug) : make_slug($request->name);

        while (self::where('slug', $slug)->exists()) {
            $slug = set_increment_slug(Contest::class, $slug);
        }

        $newEntry = self::create([
            'name' => $request->name,
            'date' => $request->date,
            'time' => $request->time,
            'price' => $request->price,
            'duration' => $request->duration,
            'image_path' => $image,
            'syllabus_title' => $request->syllabus_title,
            'description' => $request->description,
            'slug' => $slug,
            'u_id' => auth()->user()->id,
        ]);

        return $newEntry instanceof self;
    }

    public static function updateStore($request, $id): bool
    {
        $contest = Contest::find($id);

        if (!$contest) {
            return false;
        }

        $image = $contest->image_path;
        if ($request->hasFile('image_path')) {
            $image = upload_file($request->file('image_path'));
        }

        $slug = $request->slug != null ? make_slug($request->slug) : make_slug($request->name) . '-' . get_random_number(10);

        if ($slug != $contest->slug) {
            while (self::where('slug', $slug)->exists()) {
                $slug = set_increment_slug(Contest::class, $slug);
            }
        }

        return $contest->update([
            'name' => $request->name,
            'date' => $request->date,
            'time' => $request->time,
            'price' => $request->price,
            'duration' => $request->duration,
            'image_path' => $image,
            'syllabus_title' => $request->syllabus_title,
            'description' => $request->description,
            'slug' => $slug,
            'status' => $request->status,
        ]);
    }
}
