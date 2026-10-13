<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Optional ?per_page= for list endpoints the app pages through.
     * null (the default for every existing client) means "keep the
     * endpoint's own page size"; values are clamped to 1..$max so a client
     * can't ask for an unbounded page.
     */
    protected function requestedPerPage(\Illuminate\Http\Request $request, int $max = 100): ?int
    {
        $value = $request->query('per_page');
        if (!is_numeric($value)) {
            return null;
        }
        $n = (int) $value;
        return $n >= 1 ? min($n, $max) : null;
    }
}
