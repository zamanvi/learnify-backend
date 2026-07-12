<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GzipResponse
{
    /**
     * Compresses the response body when the client accepts gzip and the
     * body is large enough that compression overhead is worth it. Only
     * changes how bytes travel over the wire - the JSON content itself
     * is unchanged, the client's HTTP layer decompresses transparently.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (
            str_contains((string) $request->header('Accept-Encoding'), 'gzip')
            && !$response->headers->has('Content-Encoding')
            && method_exists($response, 'getContent')
        ) {
            $content = $response->getContent();
            if (is_string($content) && strlen($content) > 860) {
                $compressed = gzencode($content, 6);
                if ($compressed !== false) {
                    $response->setContent($compressed);
                    $response->headers->set('Content-Encoding', 'gzip');
                    $response->headers->set('Content-Length', (string) strlen($compressed));
                    $response->headers->set('Vary', 'Accept-Encoding');
                }
            }
        }

        return $response;
    }
}
