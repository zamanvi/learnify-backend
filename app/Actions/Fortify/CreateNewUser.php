<?php
namespace App\Actions\Fortify;
use App\Models\Friend;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;
    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();
        $redrose_id = str_replace(' ', '', Str::lower($input['name']) . rand(100, 99999));
        $user = User::create([
            'user_type' => '2',
            'redrose_id' => $redrose_id,
            'name' => $input['name'],
            'email' => $input['email'],
            'as_user' => 'general',
            'status' => true,
            'password' => Hash::make($input['password']),
            'date' => now()->toDateString(),
            'once' => 'no',
            'points' => '10',
        ]);
        if ($user) {
            Friend::create([
                'user_id' => $user->id,
                'friend_id' => '2',
                'type' => '1',
            ]);
            Notification::create([
                'user_id' => $user->id,
                'name' => $input['name'] . ' User Created successful.!',
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'type' => 'user',
                'status' => '1',
            ]);
            return $user;
        } else {
            return back()->with('error', 'Already have an account, please try again with diffrent email address.!');
        }
    }
}
