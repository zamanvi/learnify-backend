<?php

namespace App\Http\Controllers\Api\v2\Game;

use App\Http\Controllers\Controller;
use App\Models\Word;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    // GET /api/app/game/daily-word
    public function daily_word()
    {
        $word = Word::where('status', 1)->inRandomOrder()->first();

        if (!$word) {
            return response()->json(['success' => false, 'message' => 'No word found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id'       => $word->id,
                'word'     => $word->word,
                'meaning'  => $word->meaning,
                'synonyms' => $word->synonyms,
                'antonyms' => $word->antonyms,
                'type'     => $word->type,
            ]
        ]);
    }

    // GET /api/app/game/quiz/{lesson_id}
    public function quiz($lesson_id)
    {
        $words = Word::where('lesson_id', $lesson_id)
            ->where('status', 1)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        if ($words->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No words found for this lesson'], 404);
        }

        $allMeanings = Word::where('status', 1)
            ->whereNotIn('id', $words->pluck('id'))
            ->inRandomOrder()
            ->limit(30)
            ->pluck('meaning')
            ->toArray();

        $questions = $words->map(function ($word) use ($allMeanings) {
            $wrongOptions = array_slice(array_diff($allMeanings, [$word->meaning]), 0, 3);
            while (count($wrongOptions) < 3) {
                $wrongOptions[] = 'N/A';
            }
            $options = array_merge([$word->meaning], $wrongOptions);
            shuffle($options);

            return [
                'word'          => $word->word,
                'correct'       => $word->meaning,
                'options'       => $options,
                'synonyms'      => $word->synonyms,
                'antonyms'      => $word->antonyms,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $questions
        ]);
    }

    // POST /api/app/game/xp  (auth required)
    // Body: { score: int, total: int }
    public function add_xp(Request $request)
    {
        $request->validate([
            'score' => 'required|integer|min:0',
            'total' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $earned = (int) round(($request->score / $request->total) * 10);
        $user->increment('points', $earned);

        return response()->json([
            'success' => true,
            'data' => [
                'earned' => $earned,
                'total_points' => $user->fresh()->points,
            ]
        ]);
    }

    // GET /api/app/game/leaderboard  (public)
    public function leaderboard()
    {
        $users = User::select('id', 'name', 'points')
            ->orderByDesc('points')
            ->limit(50)
            ->get()
            ->map(function ($u, $index) {
                return [
                    'rank'   => $index + 1,
                    'name'   => $u->name,
                    'points' => $u->points,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $users
        ]);
    }

    // GET /api/app/game/streak  (auth required)
    public function streak()
    {
        $user = Auth::user();

        return response()->json([
            'success' => true,
            'data' => [
                'streak_days' => $user->streak_days ?? 0,
                'last_played' => $user->last_played_at ?? null,
            ]
        ]);
    }

    // POST /api/app/game/streak/update  (auth required)
    public function update_streak(Request $request)
    {
        $user = Auth::user();
        $today = now()->toDateString();
        $lastPlayed = optional($user->last_played_at)->toDateString();

        if ($lastPlayed === $today) {
            return response()->json([
                'success' => true,
                'data' => ['streak_days' => $user->streak_days ?? 0, 'message' => 'already_updated']
            ]);
        }

        $yesterday = now()->subDay()->toDateString();
        $newStreak = ($lastPlayed === $yesterday) ? (($user->streak_days ?? 0) + 1) : 1;

        $user->update([
            'streak_days'    => $newStreak,
            'last_played_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'data' => ['streak_days' => $newStreak]
        ]);
    }
}
