<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Repositories\WordRepositoryInterface;
use Illuminate\Http\Response;

class WordController extends Controller
{
    protected $wordRepository;

    public function __construct(WordRepositoryInterface $wordRepository) {
        $this->wordRepository = $wordRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    public function chapters_lessons_words_create($id)
    {
        $words = $this->wordRepository->getAllById($id);
        $clientType = get_client_type() ?? 'web';
        if ($clientType === 'app') {
            return ApiResponse::respond(['words' => $words], true, 'All words', Response::HTTP_OK);
        }else{
            $lesson = Lesson::find($id);
            return view('admin.word.index', compact('words', 'lesson'));
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->except('_token');

        $this->wordRepository->store($data);
        return back();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $word = $this->wordRepository->findById($id);
        $clientType = get_client_type() ?? 'web';
        if ($clientType === 'app') {
            return ApiResponse::respond(['word' => $word], true, 'Single words', Response::HTTP_OK);
        }else{
            $lesson = Lesson::find($word->lesson_id);
            $words = $this->wordRepository->getAllById($word->lesson_id);
            return view('admin.word.show', compact('words', 'word', 'lesson'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $word = $this->wordRepository->findById($id);
        $lesson = Lesson::find($word->lesson_id);
        $words = $this->wordRepository->getAllById($word->lesson_id);
        return view('admin.word.edit', compact('words', 'word', 'lesson'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->except('_token');
        $this->wordRepository->update($id, $data);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fword = $this->wordRepository->findById($id);
        $word = $this->wordRepository->delete($id);
        if ($word) {
            return redirect()->route('chapters.lessons.words.create', $fword->lesson_id)->with(['success', 'Word delete Succesfull.!']);
        }else {
            return back()->with(['error', 'Word not deleted.!']);
        }
    }
}
