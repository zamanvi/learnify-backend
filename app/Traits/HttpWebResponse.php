<?php
namespace App\Traits;

use App\Models\Notification;
use App\Models\User;

trait HttpWebResponse
{
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
