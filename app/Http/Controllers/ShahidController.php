<?php

namespace App\Http\Controllers;

use App\Models\Shahid;
use App\Traits\HttpWebResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ShahidController extends Controller
{
    use HttpWebResponse;
    function shahid_create(Request $request)
    {
        $shahids = Shahid::paginate(get_pagination(10));
        return view('admin.shahid.index', compact('shahids'));
    }
    function shahid_store(Request $request)
    {
        $user = Auth::user();
        $this->notification($user->id, '"' . $user->name . '" add new shahid "' . $request['name'] . '" successful.!', 'shahid', '1');
        $shahid = Shahid::createStore($request);
        if ($shahid) {
            return back()->with('success', 'New shahid "' . $request['name'] . '" added successfull.!');
        } else {
            return back()->with('warning', 'Error check all data again and submit again...!');
        }
    }
    function shahid_show($slug)
    {
        $shahid = Shahid::where('slug', $slug)->first();
        $shahids = Shahid::paginate(20);
        return view('admin.shahid.show', compact('shahid', 'shahids'));
    }
    function shahid_edit($slug)
    {
        $shahid = Shahid::where('slug', $slug)->first();
        $shahids = Shahid::paginate(20);
        return view('admin.shahid.edit', compact('shahid', 'shahids'));
    }
    function shahid_update(Request $request, $id)
    {
        $user = Auth::user();
        $this->notification($user->id, '"' . $user->name . '" update shahid "' . $request['name'] . '" successful.!', 'shahid', '1');
        $shahid = Shahid::updateStore($request, $id);
        if ($shahid) {
            return redirect(route('shahid.show', Shahid::find($id)->slug))->with('success', 'Shahid "' . $request['name'] . '" Update successfull.!');
        } else {
            return back()->with('warning', 'Error check all data again and submit again...!');
        }
    }
    function shahid_delete($id)
    {
        if (env('FILESYSTEM_DRIVER') == 'public') {
            $path = (env('APP_ENV') == 'local' && env('APP_DEBUG')) ? 'uploads/local/' : 'uploads/';
        }else{
            $path = (env('APP_ENV') == 'local' && env('APP_DEBUG')) ? 'local/' : 'uploads/';
        }
        $user = Auth::user();
        $shahid = Shahid::findOrFail($id);
        if (!empty($shahid->thumbnail_path)) {
            if (file_exists($path . $shahid->thumbnail_path)) {
                unlink($path . $shahid->thumbnail_path);
            }
        }

        $gallery_paths = json_decode($shahid->gallery_path, true);
        if (!empty($gallery_paths)) {
            foreach ($gallery_paths as $gallery_path) {
                if (file_exists($path . $gallery_path)) {
                    unlink($path . $gallery_path);
                }
            }
        }
        $shahid->delete();
        $this->notification($user->id, '"' . $user->name . '" delete shahid "' . $shahid->name . '" successful.!', 'shahid', '1');
        return redirect(route('shahid.create'))->with('warning', 'shahid "' . $shahid->name . '" delete successfull.!');
    }
}
