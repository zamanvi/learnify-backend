<?php

namespace App\Http\Middleware;

use App\Helpers\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use \Symfony\Component\HttpFoundation\Response;

class GrammerApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasHeader('x-api-key')) {
            if ($request->header('x-api-key') === 'app') {
                $request->attributes->set('clientType', $request->header('x-api-key'));
                return $next($request);
            }else {
                return ApiResponse::respond(null, false, 'Public Key not match', Response::HTTP_NOT_FOUND);
            }
        } else {
            return ApiResponse::respond(null, false, 'Directly access not allowed', Response::HTTP_NOT_FOUND);
        }
    }
}
