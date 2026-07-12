<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Illuminate\Http\Response;

class SettingController extends Controller
{
    /**
     * API: return all settings as key-value object
     */
    public function apiIndex(SettingRepositoryInterface $repo)
    {
        return ApiResponse::respond(['settings' => $repo->getAllSetting()], true, 'All settings', Response::HTTP_OK);
    }
}