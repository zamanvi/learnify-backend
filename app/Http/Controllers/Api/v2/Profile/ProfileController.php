<?php

namespace App\Http\Controllers\Api\v2\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // POST /api/app/profile/photo (multipart: photo)
    // The Android client already crops (1:1) and downscales/compresses the
    // image to ~512x512 before upload, so no server-side resizing is done
    // here - just validated storage, reusing the existing upload_file()/
    // get_file() helpers (app/Http/Helper.php) that the User model's
    // profile_photo accessor already reads from.
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:1024',
        ]);

        $user = $request->user();

        $filename = upload_file($request->file('photo'));

        if (!$filename) {
            return response()->json([
                'status'  => 'error',
                'message' => 'ছবি আপলোড করা যায়নি',
            ], 422);
        }

        $user->profile_photo_path = $filename;
        $user->save();

        return response()->json([
            'status'        => 'success',
            'profile_photo' => $user->profile_photo,
        ]);
    }
}
