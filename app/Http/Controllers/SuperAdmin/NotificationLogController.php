<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;

class NotificationLogController extends Controller
{
    public function index()
    {
        $logs = \App\Models\NotificationLog::latest()->paginate(20);
        return view('admin.notification.logs', compact('logs'));
    }
}
