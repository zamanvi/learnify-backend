<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiveRequest extends Model
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
    public function receiveRequest()
    {
        return $this->belongsTo(User::class, 'request_id');
    }
    public static function allReceiveRequest($id) {
        $receiveRequests = ReceiveRequest::where('request_id', $id)->pluck('user_id');
        return User::whereIn('id', $receiveRequests)
            ->select('id', 'user_type', 'redrose_id', 'name', 'email', 'as_user', 'status', 'profile_photo_path')
            ->paginate(10);
    }
}
