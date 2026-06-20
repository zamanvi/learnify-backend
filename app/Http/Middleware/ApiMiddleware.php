<?php

namespace App\Http\Middleware;

use App\Traits\AppResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('public_key') || $request->hasHeader('x-api-key')) {
            if ($this->check_public_key($request->public_key)) {
                return $next($request);
            }elseif ($this->check_public_key($request->header('x-api-key'))) {
                return $next($request);
            } else {
                return response()->json(['error' => 'Public Key not match' , 'code' => AppResponse::HTTP_NOT_FOUND]);
            }
        } else {
            // $customKey = 'Aabmn@!0171#Asha@Bizli#RRBC1234';
            // $value = 'ThisisanencryptedpublickeyforRedRoseWebApplication';
            // try {
            //     $encryptedData = Crypt::encryptString($value, $customKey);
            //     return response()->json($encryptedData);
            // } catch (\Illuminate\Contracts\Encryption\EncryptException $e) {
            //     return response()->json(['error' => 'Encryption failed', 'message' => $e->getMessage()], 500);
            // }
            return response()->json(['error' => 'Directly access not allowed', 'code' => AppResponse::HTTP_NOT_FOUND]);
        }
    }

    function check_public_key($public_key): bool
    {
        $customKey = 'Aabmn@!0171#Asha@Bizli#RRBC1234';
        $data = 'ThisisanencryptedpublickeyforRedRoseWebApplication';
        try {
            $decryptedData = Crypt::decryptString($public_key, $customKey);

            if ($data == $decryptedData) {
                return true;
            }
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            // Handle decryption error
            Log::error('Decryption failed: ' . $e->getMessage());
        }
        return false;
    }
}
