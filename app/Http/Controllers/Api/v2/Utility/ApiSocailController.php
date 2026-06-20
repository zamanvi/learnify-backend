<?php

namespace App\Http\Controllers\Api\v2\Utility;

use App\Http\Controllers\Controller;
use App\Models\Friend;
use App\Models\ReceiveRequest;
use App\Models\SendRequest;
use App\Models\User;
use App\Traits\AppResponse;
use App\Traits\HttpAppResponse;
use Illuminate\Support\Facades\Auth;

class ApiSocailController extends Controller
{
    use HttpAppResponse;
    public function allFriend()
    {
        $friends = Friend::where('user_id', Auth::id())->pluck('friend_id');
        $uesrs = User::whereIn('id', $friends)
            ->select('id', 'user_type', 'redrose_id', 'name', 'email', 'as_user', 'status', 'profile_photo_path')
            ->paginate(10);
        return $this->apiResponse(['uesrs' => $uesrs], true, 'Get all friends.', AppResponse::HTTP_OK);
    }
    public function addFriend($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->apiResponse(null, false, 'User not found!', AppResponse::HTTP_NOT_FOUND);
        }
        if (Friend::areFriends(Auth::id(), $id)) {
            return $this->apiResponse(null, false, 'Already friends.', AppResponse::HTTP_ALREADY_REPORTED);
        }
        if (SendRequest::areSendRequest(Auth::id(), $id)) {
            return $this->apiResponse(null, false, 'Already friend request send.', AppResponse::HTTP_ALREADY_REPORTED);
        }
        if ($id == Auth::id()) {
            return $this->apiResponse(null, false, 'Cannot add yourself as a friend.', AppResponse::HTTP_NOT_ACCEPTABLE);
        }
        Friend::sendFriendRequest(Auth::id(), $id);
        $this->notification(Auth::id(), '"' . Auth::user()->name . '" sent a friend request to "' . $user->name . '".', 'request', '1');
        return $this->apiResponse(null, true, 'Friend request sent successfully.', AppResponse::HTTP_OK);
    }
    public function removeFriend($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->apiResponse(null, false, 'User not found!', AppResponse::HTTP_NOT_FOUND);
        }
        if (Friend::removeFriendRequest(Auth::id(), $id)) {
            $this->notification(Auth::id(), '"' . Auth::user()->name . '" removed friend request.', 'friend', '1');
            return $this->apiResponse(null, true, 'Friend request removed successfully.', AppResponse::HTTP_OK);
        }
        return $this->apiResponse(null, false, 'Friend request dose not exist.', AppResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
    public function unfriendFriend($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->apiResponse(null, false, 'User not found!', AppResponse::HTTP_NOT_FOUND);
        }
        if (!Friend::areFriends(Auth::id(), $id)) {
            return $this->apiResponse(null, false, 'Not friends yet.', AppResponse::HTTP_NOT_FOUND);
        }
        if (Friend::unfriendFriendRequest(Auth::id(), $id)) {
            $this->notification(Auth::id(), '"' . Auth::user()->name . '" unfriend a friend.', 'friend', '1');
            return $this->apiResponse(null, true, 'Friend unfriend successful.', AppResponse::HTTP_OK);
        }
        return $this->apiResponse(null, false, 'Friend dose not exist.', AppResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
    public function confirmFriend($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->apiResponse(null, false, 'User not found!', AppResponse::HTTP_NOT_FOUND);
        }
        if (Friend::areFriends(Auth::id(), $id)) {
            return $this->apiResponse(null, false, 'Already friends yet.', AppResponse::HTTP_ALREADY_REPORTED);
        }
        if (Friend::confirmFriendRequest(Auth::id(), $id)) {
            $this->notification(Auth::id(), '"' . Auth::user()->name . '" unfriend a friend.', 'friend', '1');
            return $this->apiResponse(null, true, 'Friend request confirm successful.', AppResponse::HTTP_OK);
        }
        return $this->apiResponse(null, false, 'Friend dose not exist.', AppResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
    public function sendFriend()
    {
        return $this->apiResponse(['uesrs' => SendRequest::allSendRequest(Auth::id())], true, 'Get all send friend request.', AppResponse::HTTP_OK);
    }
    public function receivedFriend()
    {
        return $this->apiResponse(['uesrs' => ReceiveRequest::allReceiveRequest(Auth::id())], true, 'Get all receive friend request.', AppResponse::HTTP_OK);
    }
}
