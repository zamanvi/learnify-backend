<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Services\FcmService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NoticeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notices = Notice::latest()->paginate(20);
        return view('admin.notices.index', compact('notices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $notices = Notice::paginate(20);
        return view('admin.notices.index', compact('notices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, FcmService $fcm)
    {
        $notice = new Notice();
        $notice->title = $request->title;
        $notice->app = $request->app;
        $notice->body = $request->body;
        $notice->save();
        $fcm->sendToTopic('abmn', Str::limit($notice->title, 50), $request->content, [
            'type' => 'notice',
            'id' => (string) $notice->id,
        ]);
        return redirect()->route('notices.index')->with('success', 'Notice created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $notice = Notice::findOrFail($id);
        $notices = Notice::paginate(20);
        return view('admin.notices.show', compact('notice', 'notices'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $notice = Notice::findOrFail($id);
        $notices = Notice::paginate(20);
        return view('admin.notices.edit', compact('notice', 'notices'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id, FcmService $fcm)
    {
        $notice = Notice::findOrFail($id);
        $notice->title = $request->title;
        $notice->app = $request->app;
        $notice->body = $request->body;
        $notice->save();
        $fcm->sendToTopic('abmnmenglish', Str::limit($notice->title, 50), 'Add Notice, Check this out', [
            'type' => 'notice',
            'id' => (string) $notice->id,
        ]);
        return redirect()->route('notices.index')->with('success', 'Notice update successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $notice = Notice::findOrFail($id);
        $notice->delete();
        return redirect()->route('notices.index')->with('success', 'Notice deleted successfully.');
    }
}
