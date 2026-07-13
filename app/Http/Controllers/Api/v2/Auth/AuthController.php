<?php

namespace App\Http\Controllers\Api\v2\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordMail;
use App\Models\Friend;
use App\Models\Notification;
use App\Models\Otp;
use App\Models\User;
use App\Traits\AppResponse;
use App\Traits\HttpAppResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Laravel\Jetstream\Jetstream;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use HttpAppResponse;
    // old user handaling,
    public function old_user_create(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => 'required',
            'password_confirmation' => 'required',
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ]);
        $user = User::create([
            'user_type' => '2',
            'redrose_id' => $request['redrose_id'],
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'date' => now()->toDateString(),
            'once' => 'no',
            'phone' => $request['phone'],
            'points' => $request['points'],
        ]);
        if ($user) {
            Friend::create([
                'user_id' => $user->id,
                'friend_id' => '2',
                'type' => '1',
            ]);
            Notification::create([
                'user_id' => $user->id,
                'name' => $request['name'] . ' User Create from app successful.!',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'type' => 'user',
                'status' => '1',
            ]);
            return $this->apiResponse($user, true, 'User Created Successfull.!', AppResponse::HTTP_OK);
        } else {
            return $this->apiResponse('', false, "User can't Create.!", AppResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
    // old user handaling end
    // Login in function start
    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'password' => 'required',
        ]);

        $user = User::whereRaw('LOWER(name) = ?', [strtolower($request->name)])->first();
        if ($user && Hash::check($request->password, $user->password)) {
            Notification::create([
                'user_id' => $user->id,
                'name' => $user->name . ' Login by name.!',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'type' => 'login',
                'status' => '1',
            ]);
            return $this->apiResponse(
                [
                    'user' => $user,
                    'token' => $user->createToken('API Token of ' . $user->name)->plainTextToken,
                ],
                true,
                'You have successfully logged in.!',
                AppResponse::HTTP_OK,
            );
        }
        return $this->apiResponse('', false, 'নাম বা পাসওয়ার্ড ভুল', AppResponse::HTTP_UNAUTHORIZED);
    }
    // Register function start
    public function register(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $redrose_id = Str::slug($request->name) . rand(100, 99999);

        $userData = [
            'user_type' => '2',
            'redrose_id' => $redrose_id,
            'name'      => $request['name'],
            'email'     => $request['email'],
            'password'  => Hash::make($request['password']),
            'as_user'   => 'student',
            'status'    => true,
            'date'      => now()->toDateString(),
            'once'      => 'no',
            'phone'     => '01000000000',
            'points'    => '10',
        ];

        // Defensive: don't assume the friend_code migration has already run
        // on this environment's DB - inserting a column that doesn't exist
        // yet would 500 every signup, which is worse than just skipping it
        // for the (brief) window before the migration lands.
        if (\Illuminate\Support\Facades\Schema::hasColumn('users', 'friend_code')) {
            $userData['friend_code'] = User::generateFriendCode();
        }

        $user = User::create($userData);
        if ($user) {
            Friend::create([
                'user_id' => $user->id,
                'friend_id' => '2',
                'type' => '1',
            ]);
            Notification::create([
                'user_id' => $user->id,
                'name' => $request['name'] . ' User Create from app successful.!',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'type' => 'user',
                'status' => '1',
            ]);
            return $this->apiResponse(
                [
                    'user' => $user,
                    'token' => $user->createToken('API Token of ' . $user->name)->plainTextToken,
                ],
                true,
                'Account created successfully.',
                AppResponse::HTTP_CREATED,
            );
        } else {
            return $this->apiResponse('', false, "Could not create account.", 401);
        }
    }

    public function forget_password(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->input('email');
        $user  = User::where('email', $email)->first();

        if (!$user) {
            return $this->apiResponse('', false, 'এই email-এ কোনো অ্যাকাউন্ট নেই।', AppResponse::HTTP_NOT_FOUND);
        }

        $random  = rand(100000, 999999);
        $content = [
            'subject' => 'Master English Book — Password Reset OTP',
            'name'    => $user->name,
            'otp'     => $random,
        ];

        try {
            Mail::to($email)->send(new ForgotPasswordMail($content));

            $old = Otp::where('email', $email)->first();
            if ($old) {
                Otp::updateStore($email, $random, $old->id);
            } else {
                Otp::createStore($email, $random);
            }

            return $this->apiResponse(['email' => $email], true, 'OTP পাঠানো হয়েছে। Email চেক করো।', AppResponse::HTTP_OK);
        } catch (\Exception $e) {
            return $this->apiResponse('', false, 'Email পাঠাতে ব্যর্থ: ' . $e->getMessage(), AppResponse::HTTP_NOT_ACCEPTABLE);
        }
    }
    function confirm_password_verify(Request $request)
    {
        $data = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'otp' => $request->input('otp'),
        ];
        $roules = [
            'email' => 'required',
            'password' => 'required',
            'otp' => 'required',
        ];

        if ($this->request_validator($data, $roules)) {
            return $this->apiresponse('', false, 'Make sure you need to fill all the required parametter.', AppResponse::HTTP_NOT_ACCEPTABLE);
        } else {
            $email = $request->input('email');

            $otp = Otp::where('email', $email)->first();
            if ($request->otp == $otp->otp) {
                $user = User::where('email', $email)->first();
                User::where('id', $user->id)->update(['password' => Hash::make($request->password)]);
                Otp::destroyStore($otp->id);
                return $this->apiresponse('',true,'You successfully change your account password with Email',AppResponse::HTTP_OK);
            } else {
                return $this->apiresponse('', false, 'OTP Dose not match, please try again latter.!', AppResponse::HTTP_UNPROCESSABLE_ENTITY);
            }
        }
    }

    // Logout start
    public function logout(Request $request)
    {
        $request
            ->user()
            ->currentAccessToken()
            ->delete();
        return $this->apiResponse('', true, 'You have successfully logout', 200);
    }
    public function refreshToken()
    {
        $token = $this->getToken();
        return $this->apiResponse(['token' => $token], true, 'Token refresh successful...', AppResponse::HTTP_OK);
    }
}
