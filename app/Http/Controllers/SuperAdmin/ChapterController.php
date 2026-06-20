<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Models\Lesson;
use App\Repositories\ChapterRepositoryInterface;
use Illuminate\Http\Response;

class ChapterController extends Controller
{
    protected $chapterRepository;

    public function __construct(ChapterRepositoryInterface $chapterRepository) {
        $this->chapterRepository = $chapterRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function initial()
    {
        $lessons = Lesson::count();
        return ApiResponse::respond(['lessons' => $lessons], true, 'All lessons count', Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientType = get_client_type() ?? 'web';
        $chapters = $this->chapterRepository->getAll();
        if ($clientType === 'app') {
            return ApiResponse::respond(['chapters' => $chapters], true, 'All chapters', Response::HTTP_OK);
        }else{
            return view('admin.chapter.index', compact('chapters'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $chapters = $this->chapterRepository->getAll();
        return view('admin.chapter.create', compact('chapters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->except('_token');
        $this->chapterRepository->store($data);
        return redirect(route('chapters.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $chapter = $this->chapterRepository->findById($id);
        $clientType = get_client_type() ?? 'web';
        if ($clientType === 'app') {
            return ApiResponse::respond(['chapter' => $chapter], true, 'Single Chapter', Response::HTTP_OK);
        }else{
            $chapters = $this->chapterRepository->getAll();
            return view('admin.chapter.show', compact('chapter', 'chapters'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $chapters = $this->chapterRepository->getAll();
        $chapter = $this->chapterRepository->findById($id);
        return view('admin.chapter.edit', compact('chapter', 'chapters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->except('_token');
        $this->chapterRepository->update($id, $data);
        return redirect(route('chapters.show', $id));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $chapters = $this->chapterRepository->delete($id);
        if ($chapters) {
            return back()->with(['success', 'chapter delete Succesfull.!']);
        }else {
            return back()->with(['error', 'chapter not deleted.!']);
        }
    }
}
