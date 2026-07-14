<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use Illuminate\Http\Request;
use App\Repositories\LessonRepositoryInterface;
use App\Helpers\ApiResponse;
use Illuminate\Http\Response;

class LessonController extends Controller
{
   protected $lessonRepository;

    public function __construct(LessonRepositoryInterface $lessonRepository) {
        $this->lessonRepository = $lessonRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lessons = $this->lessonRepository->getAll();
        return view('admin.lesson.index', compact('lessons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.lesson.index');
    }
    /**
     * Show the form for creating a new resource.
     */
    public function chapters_lessons_create($id)
    {
        $lessons = $this->lessonRepository->getAllById($id);
        $clientType = get_client_type() ?? 'web';
        if ($clientType === 'app') {
            return ApiResponse::respond(['lessons' => $lessons], true, 'All lessons', Response::HTTP_OK);
        }else{
            return view('admin.lesson.index', compact('id', 'lessons'));
        }
    }

    /**
     * Flat list of every lesson across all chapters, id+title only.
     * Used by the Battle "challenge a friend" lesson picker, which lets a
     * user pick any lesson regardless of which chapter it lives under.
     */
    public function allForBattle()
    {
        $lessons = \App\Models\Lesson::select('id', 'title')->orderBy('title')->get();
        return ApiResponse::respond(['lessons' => $lessons], true, 'All lessons', Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->except('_token');
        $chapter = Chapter::find($data['chapter_id']);
        $data['type'] = $chapter->type;
        if (\Illuminate\Support\Facades\Schema::hasColumn('lessons', 'is_premium')) {
            $data['is_premium'] = $request->has('is_premium');
        }
        $this->lessonRepository->store($data);
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $lesson = $this->lessonRepository->findById($id);
        $clientType = get_client_type() ?? 'web';
        if ($clientType === 'app') {
            if ($lesson) {
                return ApiResponse::respond(['lesson' => $lesson], true, 'Single lesson', Response::HTTP_OK);
            }else{
                return ApiResponse::respond(null, false, 'Lesson not found', Response::HTTP_NOT_FOUND);
            }
        }else{
            $lessons = $this->lessonRepository->getAll();
            return view('admin.lesson.show', compact('lessons', 'lesson'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $lessons = $this->lessonRepository->getAll();
        $lesson = $this->lessonRepository->findById($id);
        return view('admin.lesson.edit', compact('lessons', 'lesson'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->except('_token');
        $chapter = Chapter::find($data['chapter_id']);
        $data['type'] = $chapter->type;
        if (\Illuminate\Support\Facades\Schema::hasColumn('lessons', 'is_premium')) {
            $data['is_premium'] = $request->has('is_premium');
        }
        $this->lessonRepository->update($id, $data);
        return back();
    }

    /**
     * One-click flip of a lesson's Premium flag from the lesson list,
     * so admins don't have to open the full Edit form just for this.
     */
    public function togglePremium(string $id)
    {
        $lesson = \App\Models\Lesson::find($id);
        if (!$lesson) {
            return back()->with('error', 'Lesson not found.!');
        }
        $lesson->is_premium = !$lesson->is_premium;
        $lesson->save();
        return back()->with('success', $lesson->title . ($lesson->is_premium ? ' marked Premium.!' : ' marked Free.!'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lesson = $this->lessonRepository->delete($id);
        if ($lesson) {
            return back()->with(['success', 'Lesson delete Succesfull.!']);
        }else {
            return back()->with(['error', 'Lesson not deleted.!']);
        }
    }
}
