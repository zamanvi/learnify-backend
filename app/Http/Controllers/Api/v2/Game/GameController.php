<?php

namespace App\Http\Controllers\Api\v2\Game;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\UnlockedLesson;
use App\Models\Word;
use App\Models\User;
use App\Services\QuizQuestionBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class GameController extends Controller
{
    // Same bearer-token-optional unlock check WordController uses for the
    // word-list gate - the quiz endpoint needs the identical guard, otherwise
    // it's a back door around the Premium paywall (quiz on the full word set
    // without ever unlocking).
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

    // GET /api/game/daily-word
    public function daily_word()
    {
        $word = $this->randomWord(Word::where('status', 1));

        if (!$word) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No word found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'        => $word->id,
                'word'      => $word->word,
                'meaning'   => $word->meaning,
                'synonyms'  => $word->synonyms,
                'antonyms'  => $word->antonyms,
                'type'      => $word->type,
                'lesson_id' => $word->lesson_id,
            ],
        ]);
    }

    // GET /api/game/quiz/{lesson_id}?count=10
    // GET /api/game/quiz/{lesson_id}?word_ids=12,88,5  (fixed set - used by
    // battles so every participant gets the identical question set)
    public function quiz($lesson_id, Request $request, QuizQuestionBuilder $builder)
    {
        $lesson = Lesson::find($lesson_id);
        if ($lesson && $lesson->is_premium && !$this->isLessonUnlockedByRequest($request, $lesson_id)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'এই লেসন Premium — আগে আনলক করো',
            ], 403);
        }

        $wordIdsParam = $request->query('word_ids');
        if ($wordIdsParam) {
            $wordIds = array_values(array_filter(array_map('intval', explode(',', $wordIdsParam))));
            $questions = $builder->buildQuestionsFromWordIds($wordIds);
        } else {
            $count = min((int) $request->query('count', 10), 20);
            $wordIds = $builder->selectWordIds($lesson_id, $count);
            $questions = $builder->buildQuestionsFromWordIds($wordIds);
        }

        if (empty($questions)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No words found for this lesson',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $questions,
        ]);
    }

    // POST /api/game/xp  (auth required)
    // Body: { score: int, total: int, lesson_id: int }
    public function add_xp(Request $request)
    {
        $request->validate([
            'score' => 'required|integer|min:0',
            'total' => 'required|integer|min:1',
        ]);

        $user   = Auth::user();
        $earned = (int) round(($request->score / $request->total) * 10);
        $user->increment('points', $earned);

        $totalXp   = $user->points;
        $rank      = User::where('points', '>', $totalXp)->count() + 1;

        return response()->json([
            'status' => 'success',
            'data'   => [
                'earned'       => $earned,
                'total_xp'     => $totalXp,
                'rank'         => $rank,
            ],
        ]);
    }

    // GET /api/game/leaderboard
    public function leaderboard()
    {
        $users = User::select('id', 'name', 'points')
            ->orderByDesc('points')
            ->limit(50)
            ->get()
            ->map(function ($u, $index) {
                return [
                    'id'     => $u->id,
                    'rank'   => $index + 1,
                    'name'   => $u->name,
                    'points' => $u->points,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $users,
        ]);
    }

    // GET /api/game/streak  (auth required)
    public function streak()
    {
        $user    = Auth::user();
        $totalXp = $user->points ?? 0;
        $rank    = User::where('points', '>', $totalXp)->count() + 1;

        return response()->json([
            'status' => 'success',
            'data'   => [
                'streak_days' => $user->streak_days ?? 0,
                'last_played' => $user->last_played_at ?? null,
                'total_xp'    => $totalXp,
                'rank'        => $rank,
            ],
        ]);
    }

    // POST /api/game/streak/update  (auth required)
    public function update_streak(Request $request)
    {
        $user       = Auth::user();
        $today      = now()->toDateString();
        $lastPlayed = optional($user->last_played_at)->toDateString();

        if ($lastPlayed === $today) {
            return response()->json([
                'status' => 'success',
                'data'   => [
                    'streak_days' => $user->streak_days ?? 0,
                    'message'     => 'already_updated',
                ],
            ]);
        }

        $yesterday = now()->subDay()->toDateString();
        $newStreak = ($lastPlayed === $yesterday) ? (($user->streak_days ?? 0) + 1) : 1;

        $user->update([
            'streak_days'    => $newStreak,
            'last_played_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'streak_days' => $newStreak,
            ],
        ]);
    }

    // Picks one random row from the given query without ORDER BY RAND(),
    // which forces a full-table scan+sort on large tables.
    private function randomWord($query)
    {
        $bounds = (clone $query)->selectRaw('MIN(id) as min_id, MAX(id) as max_id')->first();
        if (!$bounds || $bounds->min_id === null) {
            return null;
        }
        $randomId = rand($bounds->min_id, $bounds->max_id);
        return (clone $query)->where('id', '>=', $randomId)->orderBy('id')->first()
            ?? (clone $query)->orderBy('id')->first();
    }
}
