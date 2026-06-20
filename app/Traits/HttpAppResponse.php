<?php
namespace App\Traits;

use App\Models\Chat;
use App\Models\ContestQuestion;
use App\Models\Friend;
use App\Models\History;
use App\Models\Message;
use App\Models\ModelQuestion;
use App\Models\ModelTestResult;
use App\Models\Notification;
use App\Models\Result;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

trait HttpAppResponse
{
    protected function request_validator($data, $roules)
    {
        $validator = Validator::make($data, $roules);
        if ($validator->fails()) {
            return true;
        } else {
            return false;
        }
    }
    protected function apiResponse($data, $status, $message = null, $code)
    {
        $response = [
            'status' => $status,
            'code' => $code,
            'message' => $message,
        ];
        if ($status) {
            // $response['data'] = $data;
            $response['data'] = ($data != null) ? $data : '';
        }
        $responseKey = $status ? 'success' : 'error';
        return response()->json([$responseKey => $response], $code);
    }
    protected function getToken(): string
    {
        request()
            ->user()
            ->currentAccessToken()
            ->delete();
        $user = User::find(Auth::user()->id);
        $token = $user->createToken('API Token of ' . $user->name)->plainTextToken;
        return $token;
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
    protected function make_message()
    {
        return $this->apiResponse(
            [
                'chats' => Chat::where('user_id', Auth::user()->id)
                    ->orwhere('receiver_id', Auth::user()->id)
                    ->get(),
            ],
            true,
            'Message send successful.!',
            200,
        );
    }
    protected function message($chat_id, $message, $sender_id, $receiver_id, $seen, $type)
    {
        Message::create([
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
    protected function result_make($contest_id, $user_id, $total_q, $r_ans, $w_ans, $total_mark, $neg_mark, $give_ans, $not_give_ans)
    {
        Result::updateOrCreate(
            [
                'contest_id' => $contest_id,
                'user_id' => $user_id,
            ],
            [
                'total_q' => $total_q,
                'r_ans' => $r_ans,
                'w_ans' => $w_ans,
                'total_mark' => $total_mark,
                'neg_mark' => $neg_mark,
                'give_ans' => $give_ans,
                'not_give_ans' => $not_give_ans,
                'is_in_com' => false,
            ]
        );
    }
    protected function modeltestresult_make($modeltest_id, $user_id, $type, $total_q, $r_ans, $w_ans, $total_mark, $neg_mark, $give_ans, $not_give_ans)
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
            'give_ans' => $give_ans,
            'not_give_ans' => $not_give_ans,
        ]);
    }
    function getCurrectAns($question_id, $optopn): bool
    {
        $contestQuestion = ContestQuestion::find($question_id);
        if ($contestQuestion->option5 == $optopn) {
            return true;
        } else {
            return false;
        }
    }
    function getCurrectAnsResult($question_id, $optopn): bool
    {
        $question = ModelQuestion::find($question_id);
        if ($question->option5 == $optopn) {
            return true;
        } else {
            return false;
        }
    }
}

class AppResponse
{
    public const HTTP_CONTINUE = 100;
    public const HTTP_SWITCHING_PROTOCOLS = 101;
    public const HTTP_PROCESSING = 102; // RFC2518
    public const HTTP_EARLY_HINTS = 103; // RFC8297
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_ACCEPTED = 202;
    public const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_RESET_CONTENT = 205;
    public const HTTP_PARTIAL_CONTENT = 206;
    public const HTTP_MULTI_STATUS = 207; // RFC4918
    public const HTTP_ALREADY_REPORTED = 208; // RFC5842
    public const HTTP_IM_USED = 226; // RFC3229
    public const HTTP_MULTIPLE_CHOICES = 300;
    public const HTTP_MOVED_PERMANENTLY = 301;
    public const HTTP_FOUND = 302;
    public const HTTP_SEE_OTHER = 303;
    public const HTTP_NOT_MODIFIED = 304;
    public const HTTP_USE_PROXY = 305;
    public const HTTP_RESERVED = 306;
    public const HTTP_TEMPORARY_REDIRECT = 307;
    public const HTTP_PERMANENTLY_REDIRECT = 308; // RFC7238
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_PAYMENT_REQUIRED = 402;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_NOT_ACCEPTABLE = 406;
    public const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    public const HTTP_REQUEST_TIMEOUT = 408;
    public const HTTP_CONFLICT = 409;
    public const HTTP_GONE = 410;
    public const HTTP_LENGTH_REQUIRED = 411;
    public const HTTP_PRECONDITION_FAILED = 412;
    public const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    public const HTTP_REQUEST_URI_TOO_LONG = 414;
    public const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    public const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const HTTP_EXPECTATION_FAILED = 417;
    public const HTTP_I_AM_A_TEAPOT = 418; // RFC2324
    public const HTTP_MISDIRECTED_REQUEST = 421; // RFC7540
    public const HTTP_UNPROCESSABLE_ENTITY = 422; // RFC4918
    public const HTTP_LOCKED = 423; // RFC4918
    public const HTTP_FAILED_DEPENDENCY = 424; // RFC4918
    public const HTTP_TOO_EARLY = 425; // RFC-ietf-httpbis-replay-04
    public const HTTP_UPGRADE_REQUIRED = 426; // RFC2817
    public const HTTP_PRECONDITION_REQUIRED = 428; // RFC6585
    public const HTTP_TOO_MANY_REQUESTS = 429; // RFC6585
    public const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431; // RFC6585
    public const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451; // RFC7725
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public const HTTP_NOT_IMPLEMENTED = 501;
    public const HTTP_BAD_GATEWAY = 502;
    public const HTTP_SERVICE_UNAVAILABLE = 503;
    public const HTTP_GATEWAY_TIMEOUT = 504;
    public const HTTP_VERSION_NOT_SUPPORTED = 505;
    public const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506; // RFC2295
    public const HTTP_INSUFFICIENT_STORAGE = 507; // RFC4918
    public const HTTP_LOOP_DETECTED = 508; // RFC5842
    public const HTTP_NOT_EXTENDED = 510; // RFC2774
    public const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;
}
