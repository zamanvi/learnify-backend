<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $appends = ['profile_photo'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_type', 'redrose_id', 'name', 'email', 'as_user', 'status', 'is_first', 'password', 'date', 'once', 'points', 'bio', 'designation', 'birthday', 'gender', 'about', 'phone', 'institute', 'address', 'upazila_id', 'city_id', 'division_id', 'country_id', 'company_name', 'whatsapp', 'facebook', 'twitter', 'instagram', 'linkedin', 'pinterest', 'tiktok', 'wechat', 'title', 'slug', 'title_description', 'expected_salary', 'period_class', 'duration', 'place_of_learning', 'through_of_learning', 'tuition_type', 'tuition_class', 'tuition_subject', 'tuition_time', 'medium', 'status_for_tuition', 'about_teacher', 'about_teaching', 'degree', 'year', 'institute', 'group_subject', 'result', 'degree_proof', 'area_country', 'area_division', 'area_city', 'area_upazila', 'area_post_office', 'area_union', 'area_village', 'area_road_house', 'update_status', 'remark', 'institution', 'class_department', 'roll', 'group_subject', 'about_student'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token', 'two_factor_recovery_codes', 'two_factor_secret'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function getProfilePhotoAttribute()
    {
        if ($this->profile_photo_path != null) {
            $computedPhoto = get_file($this->profile_photo_path, 'user');
            return $computedPhoto;
        } else {
            $defaultPhoto = empty_image('user');
            return $defaultPhoto;
        }
    }
    public function notification()
    {
        return $this->hasMany(Notification::class);
    }
    public function support()
    {
        return $this->hasMany(Support::class);
    }
    public function friend()
    {
        return $this->hasMany(Friend::class);
    }
    public function send_request()
    {
        return $this->hasMany(SendRequest::class);
    }
    public function receive_request()
    {
        return $this->hasMany(ReceiveRequest::class);
    }
    public function message()
    {
        return $this->hasMany(Message::class);
    }
    public function history()
    {
        return $this->hasMany(History::class);
    }
    public function blog()
    {
        return $this->hasMany(Blog::class);
    }

}
