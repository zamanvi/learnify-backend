<?php
namespace App\Traits;

use App\Models\Blog;
use App\Models\Friend;
use App\Models\History;
use App\Models\Message;
use App\Models\ModelTestResult;
use App\Models\Notification;
use App\Models\Result;
use App\Models\User;

trait HttpWebResponse
{
    protected function success($data, $message = null, $code)
    {
        return response()->json([
            'status' => true,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ]);
    }
    protected function error($data, $message = null, $code)
    {
        return response()->json([
            'status' => false,
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ]);
    }
    protected function notification($user_id, $name, $type, $status)
    {
        Notification::create([
            'user_id' => $user_id,
            'name' => $name,
            'type' => $type,
            'status' => $status,
        ]);
    }
    protected function confirm_request($user_id, $id, $type)
    {
        Friend::create([
            'user_id' => $user_id,
            'friend_id' => $id,
            'type' => $type,
        ]);
    }
    protected function message($chat_id, $message, $sender_id, $receiver_id, $seen, $type)
    {
        $message = Message::create([
            'chat_id' => $chat_id,
            'message' => $message,
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'seen' => $seen,
            'time' => now(),
            'type' => $type,
        ]);
    }
    protected function pointupdate($id, $points)
    {
        User::where('id', $id)->update([
            'points' => $points,
        ]);
    }
    protected function history($id, $message, $type)
    {
        History::create([
            'user_id' => $id,
            'message' => $message,
            'type' => $type,
        ]);
    }
    protected function result_make($event_id, $user_id, $total_q, $r_ans, $w_ans, $total_mark, $neg_mark)
    {
        Result::create([
            'event_id' => $event_id,
            'user_id' => $user_id,
            'total_q' => $total_q,
            'r_ans' => $r_ans,
            'w_ans' => $w_ans,
            'total_mark' => $total_mark,
            'neg_mark' => $neg_mark,
        ]);
    }
    protected function modeltestresult_make($modeltest_id, $user_id, $type, $total_q, $r_ans, $w_ans, $total_mark, $neg_mark)
    {
        ModelTestResult::create([
            'modeltest_id' => $modeltest_id,
            'user_id' => $user_id,
            'type' => $type,
            'total_q' => $total_q,
            'r_ans' => $r_ans,
            'w_ans' => $w_ans,
            'total_mark' => $total_mark,
            'neg_mark' => $neg_mark,
        ]);
    }
    protected function blog_pageview_update($id, $pageview)
    {
        Blog::where('id', $id)->update([
            'pageview' => $pageview,
        ]);
    }
}
