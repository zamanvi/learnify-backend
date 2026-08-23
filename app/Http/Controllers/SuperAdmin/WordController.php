<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\UnlockedLesson;
use App\Models\Word;
use Illuminate\Http\Request;
use App\Helpers\ApiResponse;
use App\Repositories\WordRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Laravel\Sanctum\PersonalAccessToken;

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
    public function chapters_lessons_words_create(Request $request, $id)
    {
        $clientType = get_client_type() ?? 'web';
        $lesson = Lesson::find($id);

        // Premium lessons: don't send the locked part of the word list until
        // this specific user has unlocked it. Checked here (not just client-side)
        // because this endpoint has no required auth - anyone could otherwise
        // curl it directly and read the full "locked" content for free.
        if ($clientType === 'app' && $lesson && $lesson->is_premium) {
            $unlocked = $this->isLessonUnlockedByRequest($request, $id);

            if (!$unlocked) {
                $total = Word::where('lesson_id', $id)->where('status', 1)->count();
                $teaser = Word::where('lesson_id', $id)->where('status', 1)
                    ->orderBy('id')->limit(5)->get();

                return ApiResponse::respond([
                    'words' => ['data' => $teaser, 'total' => $total, 'last_page' => 1],
                    'is_premium' => true,
                    'unlocked' => false,
                    'locked_remaining' => max(0, $total - $teaser->count()),
                    'lesson_pattern' => $lesson->pattern ?? null,
                    'hint_text' => $lesson->hint_text,
                    // Per-lesson custom column names. Sent as null unless this
                    // specific lesson actually has one of col1_label..col4_label
                    // set - see the comment on the non-premium branch below for
                    // why that matters.
                    'column_labels' => $lesson->hasCustomColumnLabels() ? $lesson->column_labels : null,
                ], true, 'Premium lesson locked', Response::HTTP_OK);
            }
        }

        $words = $this->wordRepository->getAllById($id);
        if ($clientType === 'app') {
            return ApiResponse::respond([
                'words' => $words,
                'is_premium' => (bool) ($lesson->is_premium ?? false),
                'unlocked' => true,
                'lesson_pattern' => $lesson->pattern ?? null,
                'hint_text' => $lesson->hint_text,
                // null for every lesson that hasn't been given custom column
                // labels (i.e. every lesson that exists right now) - the app
                // only switches its header logic when this key is non-null,
                // so it keeps rendering the existing type/pattern-based
                // headers, completely unaware anything changed on the backend.
                'column_labels' => (optional($lesson)->hasCustomColumnLabels() ?? false) ? $lesson->column_labels : null,
            ], true, 'All words', Response::HTTP_OK);
        }else{
            return view('admin.word.index', compact('words', 'lesson'));
        }
    }

    // This endpoint has no required auth, so the user (if any) has to be
    // resolved manually from an optional Bearer token rather than $request->user().
    private function isLessonUnlockedByRequest(Request $request, $lessonId): bool
    {
        $bearer = $request->bearerToken();
        if (!$bearer) {
            return false;
        }

        $user = PersonalAccessToken::findToken($bearer)?->tokenable;
        if (!$user) {
            return false;
        }

        return UnlockedLesson::where('user_id', $user->id)
            ->where('lesson_id', $lessonId)
            ->exists();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024']);

        $data = $request->except('_token', 'image');
        if ($request->hasFile('image')) {
            $data['image'] = upload_file($request->file('image'));
        }

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
        $request->validate(['image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024']);

        $data = $request->except('_token', 'image');
        if ($request->hasFile('image')) {
            $data['image'] = upload_file($request->file('image'));
        }

        // A slow response (see Dockerfile note on PHP_CLI_SERVER_WORKERS) can
        // make an admin submit twice; the second submit's findOrFail() would
        // otherwise surface as a raw 404 if the first one already changed
        // something this update depends on (e.g. the word was deleted
        // between the two clicks). Same reasoning in destroy() below.
        try {
            $this->wordRepository->update($id, $data);
        } catch (ModelNotFoundException $e) {
            return back()->with(['error', 'This word no longer exists - it may have already been updated or deleted.']);
        }
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $fword = $this->wordRepository->findById($id);
            $word = $this->wordRepository->delete($id);
        } catch (ModelNotFoundException $e) {
            // Someone/something already deleted it (e.g. a duplicate submit) -
            // that's not really a failure from the admin's point of view.
            return back()->with(['success', 'Word already deleted.']);
        }

        if ($word) {
            return redirect()->route('chapters.lessons.words.create', $fword->lesson_id)->with(['success', 'Word delete Succesfull.!']);
        }else {
            return back()->with(['error', 'Word not deleted.!']);
        }
    }
}
