<?php
namespace App\Traits;

use App\Models\Notification;
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
    protected function pointupdate($id, $points)
    {
        User::where('id', $id)->update([
            'points' => $points,
        ]);
    }
}
