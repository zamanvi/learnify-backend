<?php

namespace App\Http\Controllers\Api\v2\Utility;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Models\Page;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Traits\AppResponse;
use App\Traits\HttpAppResponse;
use Illuminate\Http\Request;

class ApiPageController extends Controller
{
    use HttpAppResponse;
    function page_find(Request $request)
    {
        if (!$request->has('type')) {
            return $this->apiResponse([], false, 'Page type is required...!', AppResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        if (!$request->has('slug')) {
            return $this->apiResponse([], false, 'Page slug is required...!', AppResponse::HTTP_UNPROCESSABLE_ENTITY);
        }
        $page = Page::where('type', $request->type)->where('slug', $request->slug)->first();
        if (!$page) {
            return $this->apiResponse([], false, 'Page not found...!', AppResponse::HTTP_NOT_FOUND);
        }
        return $this->apiResponse(['page' => $page], true, 'Page details.', AppResponse::HTTP_OK);
    }

    function appVersion(SettingRepositoryInterface $repo)
    {
        $data['app_version'] = $repo->getSetting('app_version');
        $data['app_version_text'] = $repo->getSetting('app_version_text');
        return $this->apiResponse($data, true, 'App Version.', AppResponse::HTTP_OK);
    }
    function appNotices($app)
    {
        $notices = Notice::latest()->where('app', $app)->paginate(20);
        return $this->apiResponse(['notices' => $notices], true, 'Retrive All ' . $app . ' notices.', AppResponse::HTTP_OK);
    }
}
