<?php

namespace App\Http\Controllers\Api\v2\Game;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LiptoTransaction;
use App\Models\UnlockedLesson;
use App\Models\UserLessonRoundProgress;
use App\Models\Word;
use App\Models\User;
use App\Services\QuizQuestionBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class GameController extends Controller
{
    // roundQuiz()/quiz() sit on public routes (no auth:sanctum) so a guest
    // can still fetch a free lesson's quiz - this resolves whoever's bearer
    // token (if any) came along for the ride, same technique both the
    // Premium-unlock check and the round-sequence check below need.
    private function resolveUserFromBearer(Request $request): ?User
    {
        $bearer = $request->bearerToken();
        if (!$bearer) {
            return null;
        }

        return PersonalAccessToken::findToken($bearer)?->tokenable;
    }

    // Same bearer-token-optional unlock check WordController uses for the
    // word-list gate - the quiz endpoint needs the identical guard, otherwise
    // it's a back door around the Premium paywall (quiz on the full word set
    // without ever unlocking).
    private function isLessonUnlockedByRequest(Request $request, $lessonId): bool
    {
        $user = $this->resolveUserFromBearer($request);
        if (!$user) {
            return false;
        }

        return UnlockedLesson::where('user_id', $user->id)
            ->where('lesson_id', $lessonId)
            ->exists();
    }

    // Row-locks (or creates, retrying once on a lost create race) a round's
    // progress row - shared by levelMap()'s first-visit bootstrap and
    // submitRound()'s read-modify-write, both of which used to be plain
    // unlocked reads/creates vulnerable to the same race BattleController's
    // join() had before it was fixed this session.
    private function getOrCreateRoundProgressLocked(int $userId, int $lessonId, int $round, string $defaultStatus): UserLessonRoundProgress
    {
        $progress = UserLessonRoundProgress::where('user_id', $userId)
            ->where('lesson_id', $lessonId)
            ->where('round_number', $round)
            ->lockForUpdate()
            ->first();

        if ($progress) {
            return $progress;
        }

        try {
            return UserLessonRoundProgress::create([
                'user_id' => $userId, 'lesson_id' => $lessonId,
                'round_number' => $round, 'status' => $defaultStatus,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Lost the create race to a concurrent request - fetch what it
            // just committed instead of bubbling up an unhandled 500.
            return UserLessonRoundProgress::where('user_id', $userId)
                ->where('lesson_id', $lessonId)
                ->where('round_number', $round)
                ->lockForUpdate()
                ->firstOrFail();
        }
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

    // GET /api/game/round-quiz/{lesson_id}/{round}
    // round: 1=MCQ, 2=Reading, 3=Listening, 4=Writing
    public function roundQuiz($lesson_id, $round, Request $request, QuizQuestionBuilder $builder)
    {
        $round = (int) $round;
        if ($round < 1 || $round > 4) {
            return response()->json(['status' => 'error', 'message' => 'Invalid round'], 422);
        }

        $lesson = Lesson::find($lesson_id);
        if ($lesson && $lesson->is_premium && !$this->isLessonUnlockedByRequest($request, $lesson_id)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'এই লেসন Premium — আগে আনলক করো',
            ], 403);
        }

        // Sequential round-unlock was previously enforced only by the
        // Android level-map UI graying out locked rounds - a direct API
        // call could fetch (and via submitRound(), pass+reward) round 4
        // without ever touching 1-3. Round 1 has no prerequisite.
        if ($round > 1) {
            $user = $this->resolveUserFromBearer($request);
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'লগইন করো আগে'], 401);
            }
            $progress = UserLessonRoundProgress::where('user_id', $user->id)
                ->where('lesson_id', $lesson_id)
                ->where('round_number', $round)
                ->first();
            if (!$progress || $progress->status === 'locked') {
                return response()->json(['status' => 'error', 'message' => 'আগের রাউন্ড ক্লিয়ার করো আগে'], 403);
            }
        }

        $questions = match ($round) {
            1 => $builder->buildQuestionsFromWordIds($builder->selectWordIds($lesson_id, 10)),
            2 => $builder->buildReadingQuestions($lesson_id, 20),
            3 => $builder->buildListeningQuestions($lesson_id, 10),
            4 => $builder->buildWritingQuestions($lesson_id, 10),
        };

        if (empty($questions)) {
            return response()->json(['status' => 'error', 'message' => 'No words found for this lesson'], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'round'      => $round,
                'max_hearts' => 3,
                'questions'  => $questions,
            ],
        ]);
    }

    // GET /api/app/game/level-map/{lesson_id}  (auth required)
    public function levelMap($lesson_id)
    {
        $user = Auth::user();

        // Same paywall as quiz()/roundQuiz() - without this, a locked
        // Premium lesson's level map would render as if every round were
        // playable, and only reject the player one tap later once they
        // actually try to start Round 1. Auth is already guaranteed here
        // (this route sits behind auth:sanctum), so check the unlock
        // directly instead of the bearer-token-optional helper those two
        // public endpoints need.
        $lesson = Lesson::find($lesson_id);
        if ($lesson && $lesson->is_premium) {
            $unlocked = UnlockedLesson::where('user_id', $user->id)->where('lesson_id', $lesson_id)->exists();
            if (!$unlocked) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'এই লেসন Premium — আগে আনলক করো',
                ], 403);
            }
        }

        $rows = DB::transaction(function () use ($user, $lesson_id) {
            $existing = UserLessonRoundProgress::where('user_id', $user->id)
                ->where('lesson_id', $lesson_id)
                ->get()
                ->keyBy('round_number');

            if ($existing->isEmpty()) {
                $existing->put(1, $this->getOrCreateRoundProgressLocked($user->id, $lesson_id, 1, 'unlocked'));
            }

            return $existing;
        });

        $rounds = collect(range(1, 4))->map(function ($n) use ($rows) {
            $row = $rows->get($n);
            return [
                'round'  => $n,
                'status' => $row->status ?? 'locked',
                'stars'  => $row->stars ?? 0,
            ];
        });

        return response()->json(['status' => 'success', 'data' => $rounds]);
    }

    // POST /api/app/game/round/submit  (auth required)
    // Body: { lesson_id, round, score, total, hearts_lost }
    // Replaces the old 4-separate-POST pattern (xp/streak/lipto) with one
    // atomic call for the round-based flow - pass/fail, stars, XP, Lipto,
    // mystery box, streak, and next-round unlock all update together.
    public function submitRound(Request $request)
    {
        $request->validate([
            'lesson_id'   => 'required|integer',
            'round'       => 'required|integer|min:1|max:4',
            'score'       => 'required|integer|min:0',
            'total'       => 'required|integer|min:1|max:255',
            'hearts_lost' => 'required|integer|min:0|max:3',
        ]);

        if ($request->score > $request->total) {
            return response()->json(['status' => 'error', 'message' => 'অবৈধ score'], 422);
        }

        if (!Lesson::find($request->lesson_id)) {
            return response()->json(['status' => 'error', 'message' => 'Lesson খুঁজে পাওয়া যায়নি'], 404);
        }

        $user   = Auth::user();
        $round  = (int) $request->round;
        $passed = $request->hearts_lost < 3;

        $result = DB::transaction(function () use ($user, $request, $round, $passed) {
            $progress = $this->getOrCreateRoundProgressLocked(
                $user->id, $request->lesson_id, $round, $round === 1 ? 'unlocked' : 'locked'
            );

            // Same sequential-unlock rule as roundQuiz() - without this, a
            // direct API call could submit (and get rewarded for) a round
            // whose prerequisite was never actually cleared.
            if ($round > 1 && $progress->status === 'locked') {
                return ['locked' => true];
            }

            $progress->attempts += 1;
            $progress->last_attempt_at = now();

            $xpEarned    = 0;
            $liptoEarned = 0;
            $mysteryBox  = null;
            $stars       = $progress->stars;
            $nextUnlocked = false;

            if ($passed) {
                $stars = max($stars, $this->starsForHeartsLost($request->hearts_lost));
                $progress->status = 'passed';
                $progress->stars  = $stars;
                if ($progress->best_score === null || $request->score > $progress->best_score) {
                    $progress->best_score = $request->score;
                    $progress->best_total = $request->total;
                    $progress->hearts_lost_best_attempt = $request->hearts_lost;
                }

                $xpEarned = (int) round(($request->score / $request->total) * 10);
                $user->increment('points', $xpEarned);

                $mysteryBox  = $this->rollMysteryBox();
                $liptoEarned = $this->grantLiptoWithDailyCap($user, $mysteryBox['lipto'], "Round {$round} mystery box ({$mysteryBox['tier']})");
                $mysteryBox['lipto'] = $liptoEarned; // reflect what was actually granted (may be capped)

                if ($round < 4) {
                    $nextRound = $this->getOrCreateRoundProgressLocked(
                        $user->id, $request->lesson_id, $round + 1, 'locked'
                    );
                    if ($nextRound->status === 'locked') {
                        $nextRound->status = 'unlocked';
                        $nextRound->save();
                        $nextUnlocked = true;
                    }
                }
            }

            $progress->save();

            // Any attempt (pass or fail) counts as today's practice, same as
            // the standalone update_streak endpoint the old flow called separately.
            $streakDays = $this->bumpStreak($user);

            return compact('xpEarned', 'liptoEarned', 'mysteryBox', 'stars', 'nextUnlocked', 'streakDays');
        });

        if (isset($result['locked'])) {
            return response()->json(['status' => 'error', 'message' => 'আগের রাউন্ড ক্লিয়ার করো আগে'], 403);
        }

        return response()->json([
            'status' => 'success',
            'data'   => [
                'passed'             => $passed,
                'stars'              => $result['stars'],
                'xp_earned'          => $result['xpEarned'],
                'lipto_earned'       => $result['liptoEarned'],
                'mystery_box'        => $result['mysteryBox'],
                'next_round_unlocked'=> $result['nextUnlocked'],
                'streak_days'        => $result['streakDays'],
                'total_xp'           => $user->points,
            ],
        ]);
    }

    // Same day/yesterday logic as update_streak() below, factored out so
    // submitRound() can update the streak in the same atomic call instead
    // of relying on a separate client-fired POST.
    private function bumpStreak(User $user): int
    {
        $today      = now()->toDateString();
        $lastPlayed = optional($user->last_played_at)->toDateString();

        if ($lastPlayed === $today) {
            return $user->streak_days ?? 0;
        }

        $yesterday = now()->subDay()->toDateString();
        $newStreak = ($lastPlayed === $yesterday) ? (($user->streak_days ?? 0) + 1) : 1;

        $user->update(['streak_days' => $newStreak, 'last_played_at' => now()]);

        return $newStreak;
    }

    // Mirrors LiptoController::earn()'s daily-cap logic so mystery-box
    // rewards can't be farmed past the same DAILY_EARN_CAP by replaying
    // easy rounds - must run inside the caller's existing DB::transaction.
    private function grantLiptoWithDailyCap(User $user, int $amount, string $description): int
    {
        $locked = User::whereKey($user->id)->lockForUpdate()->first();

        $dayStart = now('Asia/Dhaka')->startOfDay();
        $earnedToday = (int) LiptoTransaction::where('user_id', $locked->id)
            ->where('type', 'earn')
            ->where('created_at', '>=', $dayStart)
            ->sum('amount');

        $grantable = max(0, min($amount, LiptoController::DAILY_EARN_CAP - $earnedToday));
        if ($grantable === 0) {
            return 0;
        }

        $locked->increment('lipto_balance', $grantable);

        LiptoTransaction::create([
            'user_id'       => $locked->id,
            'amount'        => $grantable,
            'type'          => 'earn',
            'source'        => 'mystery_box',
            'description'   => $description,
            'balance_after' => $locked->lipto_balance,
        ]);

        return $grantable;
    }

    // 70% common / 25% rare / 5% epic - starter table, tune later.
    private function rollMysteryBox(): array
    {
        $roll = rand(1, 100);
        if ($roll <= 70) {
            return ['tier' => 'common', 'lipto' => rand(5, 10)];
        }
        if ($roll <= 95) {
            return ['tier' => 'rare', 'lipto' => rand(15, 25)];
        }
        return ['tier' => 'epic', 'lipto' => rand(40, 60)];
    }

    private function starsForHeartsLost(int $heartsLost): int
    {
        return match (true) {
            $heartsLost === 0 => 3,
            $heartsLost === 1 => 2,
            default => 1,
        };
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
    // Kept for old app builds that still POST this separately - submitRound()
    // now calls bumpStreak() directly instead of relying on this being
    // called as a follow-up request.
    public function update_streak(Request $request)
    {
        $user = Auth::user();
        $alreadyUpdatedToday = optional($user->last_played_at)->toDateString() === now()->toDateString();

        $streakDays = $this->bumpStreak($user);

        return response()->json([
            'status' => 'success',
            'data'   => $alreadyUpdatedToday
                ? ['streak_days' => $streakDays, 'message' => 'already_updated']
                : ['streak_days' => $streakDays],
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
