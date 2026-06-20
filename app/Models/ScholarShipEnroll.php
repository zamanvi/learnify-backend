<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ScholarShipEnroll extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','scholar_ship_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    /**
     * Check if a user is already enrolled in a specific scholarship.
     *
     * @param int $userId
     * @param int $scholarShipId
     * @return bool
     */
    public static function isUserEnrolled($userId, $scholarShipId)
    {
        return self::where('user_id', $userId)
                    ->where('scholar_ship_id', $scholarShipId)
                    ->exists();
    }

    /**
     * Get the current number of enrollments for a specific scholarship.
     *
     * @param int $scholarShipId
     * @return int
     */
    public static function enrollmentCount($scholarShipId)
    {
        return self::where('scholar_ship_id', $scholarShipId)->count();
    }

}
