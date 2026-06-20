<?php
namespace App\Http\Controllers\Api\v2\Utility;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Traits\AppResponse;
use App\Traits\HttpAppResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ApiMessageController extends Controller
{
    use HttpAppResponse;
    protected $user_id;
    protected $user;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::check()) {
                $this->user = Auth::user();
                $this->user_id = $this->user->id;
            }
            return $next($request);
        });
    }
    public function messageList()
    {
        $chats = Chat::latest()->with(['sender:id,name,profile_photo_path', 'receiver:id,name,profile_photo_path'])
            ->where(function ($query) {
                $query->where('user_id', $this->user_id)
                    ->orWhere('sender_id', $this->user_id)
                    ->orWhere('receiver_id', $this->user_id);
            })->get();
        return $this->apiResponse(['chats' => $chats], true, 'All messages...!', AppResponse::HTTP_OK);
    }

    public function makeMessage(Request $request, $id)
    {
        if ($id === null || !is_numeric($id) || preg_match('/[^0-9]/', $id)) {
            return $this->apiResponse('', false, 'Please pass an receiver id...!', AppResponse::HTTP_NOT_ACCEPTABLE);
        }

        $user = User::find($id);
        if (!$user) {
            return $this->apiResponse('', false, 'Receiver Not found...!', AppResponse::HTTP_NOT_FOUND);
        }

        $chat = Chat::where(function ($query) use ($id) {
                $query->where('user_id', $this->user_id)
                    ->where('sender_id', $this->user_id)
                    ->where('receiver_id', $id);
            })
            ->orWhere(function ($query) use ($id) {
                $query->where('user_id', $id)
                    ->where('sender_id', $id)
                    ->where('receiver_id', $this->user_id);
            })
            ->first();

        if (!$chat) {
            $chat = Chat::create([
                'user_id' => $this->user_id,
                'sender_id' => $this->user_id,
                'receiver_id' => $id,
                'lastmessage' => $request->message,
            ]);
        } else {
            $chat->update(['lastmessage' => $request->message]);
        }
        Message::create([
            'chat_id' => $chat->id,
            'message' => $request->message,
            'sender_id' => $this->user_id,
            'receiver_id' => $id,
            'type' => $request->type,
        ]);
        return $this->apiResponse(['chat_id' => $chat->id], true, 'Make new message successful...!', AppResponse::HTTP_OK);
    }
    public function showMessage($id)
    {
        $messageList = Message::with(['sender:id,name,profile_photo_path', 'receiver:id,name,profile_photo_path'])
            ->where('chat_id', $id)
            ->paginate(100);
        $chat = Chat::find($id);
        $receiver_id = $chat->user_id == $this->user_id ? $chat->receiver_id : $chat->sender_id;
        $receiver = User::where('id', $receiver_id)->get(['id', 'email', 'name', 'profile_photo_path']);
        return $this->apiResponse(['receiver' => $receiver, 'messageList' => $messageList], true, 'All messages...!', AppResponse::HTTP_OK);
    }
    public function sendMessage(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'message' => 'required',
                'receiver_id' => 'required|integer',
                'type' => 'required|string',
            ]);
            $chat = Chat::find($id);
            DB::transaction(function () use ($request, $chat) {
                if ($request->type == 'text') {
                    $chat->lastmessage = $request->message;
                    $chat->save();
                } else if ($request->type == 'point') {
                    $point = $this->user->points;
                    if ($point > $request->message) {
                        $user_points = $this->user->points - $request->message;
                        $this->user->points = $user_points;
                        $this->user->save();

                        $receiver = User::find($request->receiver_id);
                        $receiverPoints = $receiver->points + $request->message;
                        $receiver->points = $receiverPoints;
                        $receiver->save();

                        $this->notification($this->user_id, 'You send ' . $request->message . ' points to ' . $receiver->name, 'points', 0);
                        $this->notification($request->receiver_id, 'You received ' . $request->message . ' points from ' . $this->user->name, 'points', 0);
                        $chat->lastmessage = 'point';
                        $chat->save();
                    } else {
                        return $this->apiResponse([], false, 'You don\'t have enough points, Please try again later...!', AppResponse::HTTP_INTERNAL_SERVER_ERROR);
                    }
                }
                Message::create([
                    'chat_id' => $chat->id,
                    'message' => $request->message,
                    'sender_id' => $this->user_id,
                    'receiver_id' => $request->receiver_id,
                    'type' => $request->type,
                ]);
            });
            $messageList = Message::with(['sender:id,name,profile_photo_path', 'receiver:id,name,profile_photo_path'])
                ->where('chat_id', $id)
                ->paginate(100);
            return $this->apiResponse(['messageList' => $messageList], true, 'Send new message successful...!', AppResponse::HTTP_OK);
        } catch (ValidationException $exception) {
            return $this->apiResponse([], false, $exception->getMessage(), AppResponse::HTTP_BAD_REQUEST);
        } catch (\Exception $exception) {
            return $this->apiResponse([], false, $exception->getMessage(), AppResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function editMessage(Request $request, $chat_id, $message_id)
    {
        try {
            $message = Message::findOrFail($message_id);
            if (!$message->canBeEditedBy($this->user_id)) {
                return $this->apiResponse([], false, 'You are not allowed to edit this message.', AppResponse::HTTP_FORBIDDEN);
            }
            $message->editMessage($request);
            $messageList = Message::with(['sender:id,name,profile_photo_path', 'receiver:id,name,profile_photo_path'])
                ->latest()
                ->where('chat_id', $chat_id)
                ->paginate(100);
            return $this->apiResponse(['messageList' => $messageList], true, 'Edited message successfully!', AppResponse::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->apiResponse([], false, 'Message not found.', AppResponse::HTTP_NOT_FOUND);
        }
    }
    public function unsendMessage(Request $request, $chat_id, $message_id)
    {
        try {
            $message = Message::findOrFail($message_id);
            if (!$message->canBeEditedBy($this->user_id)) {
                return $this->apiResponse([], false, 'You are not allowed to unsend this message.', AppResponse::HTTP_FORBIDDEN);
            }
            $message->unsendMessage();
            $messageList = Message::with(['sender:id,name,profile_photo_path', 'receiver:id,name,profile_photo_path'])
                ->where('chat_id', $chat_id)
                ->paginate(100);
            return $this->apiResponse(['messageList' => $messageList], true, 'Unsended message successfully!', AppResponse::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->apiResponse([], false, 'Message not found.', AppResponse::HTTP_NOT_FOUND);
        }
    }
    public function removeMessage(Request $request, $chat_id, $message_id)
    {
        try {
            $message = Message::findOrFail($message_id);
            if (!$message->canBeEditedBy($this->user_id)) {
                return $this->apiResponse([], false, 'You are not allowed to remove this message.', AppResponse::HTTP_FORBIDDEN);
            }
            $message->removeMessage($request);
            $messageList = Message::with(['sender:id,name,profile_photo_path', 'receiver:id,name,profile_photo_path'])
                ->where('chat_id', $chat_id)
                ->paginate(100);
            return $this->apiResponse(['messageList' => $messageList], true, 'Removed message successfully!', AppResponse::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->apiResponse([], false, 'Message not found.', AppResponse::HTTP_NOT_FOUND);
        }
    }
    public function deleteMessage(Request $request, $chat_id, $message_id)
    {
        try {
            $message = Message::findOrFail($message_id);
            if (!$message->canBeEditedBy($this->user_id)) {
                return $this->apiResponse([], false, 'You are not allowed to delete this message.', AppResponse::HTTP_FORBIDDEN);
            }
            $message->deleteMessage();
            $messageList = Message::with(['sender:id,name,profile_photo_path', 'receiver:id,name,profile_photo_path'])
                ->where('chat_id', $chat_id)
                ->paginate(100);
            return $this->apiResponse(['messageList' => $messageList], true, 'Deleted message successfully!', AppResponse::HTTP_OK);
        } catch (\Exception $exception) {
            return $this->apiResponse([], false, 'Message not found.', AppResponse::HTTP_NOT_FOUND);
        }
    }
}
