<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'request_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function sendRequest()
    {
        return $this->belongsTo(User::class, 'request_id');
    }

    public static function areSendRequest($senderId, $receiverId) {
        return self::where(function ($query) use ($senderId, $receiverId){
            $query->where('user_id', $senderId)->where('request_id', $receiverId);
        })->exists();
    }
    public static function allSendRequest($id) {
        $sendRequests = SendRequest::where('user_id', $id)->pluck('request_id');
        return User::whereIn('id', $sendRequests)
            ->select('id', 'user_type', 'redrose_id', 'name', 'email', 'as_user', 'status', 'profile_photo_path')
            // ->orderBy('id', 'desc')
            ->paginate(10);
    }
}
