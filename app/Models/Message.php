<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['chat_id', 'message', 'sender_id', 'receiver_id', 'seen', 'remark', 'type'];
    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'message');
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
    public function isSender($userId)
    {
        return $this->sender_id == $userId;
    }
    public function canBeEditedBy($userId)
    {
        return $this->isSender($userId);
    }
    public function editMessage($request)
    {
        $this->chat->update([
            'lastmessage' => $this->message . ' => ' . $request->message,
        ]);
        $this->update([
            'message' => $request->message,
            'remark' => 'edit',
        ]);
        return $this;
    }
    public function unsendMessage()
    {
        $this->chat->update([
            'lastmessage' => 'Unsend message',
        ]);
        $this->update([
            'remark' => 'unsend',
        ]);
        return $this;
    }
    public function removeMessage($request)
    {
        $isLatestMessage = $this->id === Message::latest()->where('chat_id', $this->chat_id)->value('id');
        if ($isLatestMessage) {
            $this->chat->update([
                'lastmessage' => 'Remove message',
            ]);
        }
        $this->update([
            'remark' => 'remove',
        ]);
        return $this;
    }
    public function deleteMessage()
    {
        $chatId = $this->chat_id;
        $messageBefore = Message::where('chat_id', $chatId)
            ->where('created_at', '<', $this->created_at)
            ->latest('created_at')
            ->first();
        Chat::where('id', $chatId)->update([
            'lastmessage' => $messageBefore ? $messageBefore->message : null,
        ]);
        $this->delete();
        return true;
    }
}
