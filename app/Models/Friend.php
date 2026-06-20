<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'friend_id',
        'type',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }

    public static function areFriends($userId1, $userId2)
    {
        return self::where(function ($query) use ($userId1, $userId2) {
            $query->where('user_id', $userId1)
                ->where('friend_id', $userId2);
        })->orWhere(function ($query) use ($userId1, $userId2) {
            $query->where('user_id', $userId2)
                ->where('friend_id', $userId1);
        })->exists();
    }
    public static function sendFriendRequest($senderId, $receiverId)
    {
        if (self::areFriends($senderId, $receiverId)) {
            return false;
        }
        SendRequest::create([
            'user_id' => $senderId,
            'request_id' => $receiverId,
        ]);
        ReceiveRequest::create([
            'user_id' => $receiverId,
            'request_id' => $senderId,
        ]);

        return true;
    }
    public static function removeFriendRequest($senderId, $receiverId)
    {
        $sendRequest = SendRequest::where('user_id', $senderId)->where('request_id', $receiverId)->first();
        if (!$sendRequest) {
            return false;
        }
        $sendRequest->delete();
        ReceiveRequest::where('user_id', $receiverId)->where('request_id', $senderId)->delete();
        return true;
    }
    public static function unfriendFriendRequest($senderId, $receiverId)
    {
        self::where('user_id', $senderId)->where('friend_id', $receiverId)->delete();
        self::where('user_id', $receiverId)->where('friend_id', $senderId)->delete();

        Message::where('sender_id', $senderId)->where('receiver_id', $receiverId)->delete();
        Message::where('sender_id', $receiverId)->where('receiver_id', $senderId)->delete();

        Chat::where('sender_id', $senderId)->where('receiver_id', $receiverId)->delete();
        Chat::where('sender_id', $receiverId)->where('receiver_id', $senderId)->delete();
        return true;
    }
    public static function confirmFriendRequest($senderId, $receiverId)
    {
        self::create([
            'user_id' => $senderId,
            'friend_id' => $receiverId
        ]);
        self::create([
            'user_id' => $receiverId,
            'friend_id' => $senderId
        ]);

        SendRequest::where('user_id', $senderId)->where('request_id', $receiverId)->delete();
        ReceiveRequest::where('user_id', $receiverId)->where('request_id', $senderId)->delete();
        return true;
    }
}
