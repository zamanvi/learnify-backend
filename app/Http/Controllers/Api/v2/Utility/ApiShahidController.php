<?php

namespace App\Http\Controllers\Api\v2\Utility;

use App\Http\Controllers\Controller;
use App\Models\Shahid;
use App\Traits\AppResponse;
use App\Traits\HttpAppResponse;
use Illuminate\Http\Request;

class ApiShahidController extends Controller
{
    use HttpAppResponse;
    function shahid_index(Request $request)
    {
        $perPage = 10;
        if ($request->has('per_page')) {
            $perPage = $request->per_page;
        }
        $shahids = Shahid::latest()->select(['id', 'slug', 'name', 'address', 'thumbnail_path', 'pageview'])->paginate($perPage);

        return $this->apiResponse(['shahids' => $shahids], true, 'All Shahid read successful.', AppResponse::HTTP_OK);
    }
    function shahid_slug($slug) {
        $shahid = Shahid::select('id', 'slug', 'name', 'address', 'thumbnail_path', 'gallery_path', 'video_link', 'description', 'pageview')
            ->where('slug', $slug)
            ->first();
        if (!$shahid) {
            return $this->apiResponse([], false, 'shahid not found.', AppResponse::HTTP_NOT_FOUND);
        }
        $shahid->pageview = $shahid->pageview + 1;
        $shahid->save();
        return $this->apiResponse(['shahid' => $shahid], true, 'shahid read successful.', AppResponse::HTTP_OK);
    }
}
