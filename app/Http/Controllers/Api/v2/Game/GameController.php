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

    // ------------------------------------------------------------------
    // Round numbering.
    //   canonical (stored in user_lesson_round_progress and used by current
    //   clients, "?v=2"): 1 MCQ, 2 Reading, 3 Picture, 4 Listening, 5 Writing.
    //   legacy clients (Play Store 1.1.7, no "v" parameter) number the rounds
    //   1 MCQ, 2 Reading, 3 Listening, 4 Writing - translated both ways here.
    // Round 3 is "Match pairs" (match 10 English words with their Bangla
    // meanings). Only clients that can play it say so with "?v=3"; older
    // ones (legacy, "?v=2") get it reported as "soon" and the unlock chain
    // skips it (2 -> 4), exactly like the switched-off Picture round did.
    // The old image-based Picture builder is no longer used.
    // ------------------------------------------------------------------
    private const MATCH_ROUND = 3;
    private const MATCH_MIN_CLIENT = 3;
    private const LEGACY_ROUND_MAP = [1 => 1, 2 => 2, 3 => 4, 4 => 5];

    private function clientVersion(Request $request): int
    {
        return (int) $request->query('v', $request->input('v', 0));
    }

    // 5-slot numbering (v >= 2); anything older is the legacy 4-round numbering
    private function isCurrentClient(Request $request): bool
    {
        return $this->clientVersion($request) >= 2;
    }

    // rounds this client can not play yet
    private function disabledRounds(Request $request): array
    {
        return $this->clientVersion($request) >= self::MATCH_MIN_CLIENT ? [] : [self::MATCH_ROUND];
    }

    // client round number -> canonical round, or null when out of range
    private function toCanonicalRound(Request $request, int $round): ?int
    {
        if ($this->isCurrentClient($request)) {
            return ($round >= 1 && $round <= 5) ? $round : null;
        }
        return self::LEGACY_ROUND_MAP[$round] ?? null;
    }

    private function nextEnabledRound(int $round, array $disabled): ?int
    {
        for ($n = $round + 1; $n <= 5; $n++) {
            if (!in_array($n, $disabled, true)) {
                return $n;
            }
        }
        return null;
    }

    private function previousEnabledRound(int $round, array $disabled): ?int
    {
        for ($n = $round - 1; $n >= 1; $n--) {
            if (!in_array($n, $disabled, true)) {
                return $n;
            }
        }
        return null;
    }

    // Players who cleared the round before a switched-off one (e.g. Round 2)
    // may have no usable row for the one after it (Round 4 stays "locked"
    // while Round 3 sat "unlocked" forever). Opens it for them - idempotent.
    private function healRoundUnlocks(int $userId, int $lessonId, array $disabled): void
    {
        DB::transaction(function () use ($userId, $lessonId, $disabled) {
            $rows = UserLessonRoundProgress::where('user_id', $userId)
                ->where('lesson_id', $lessonId)
                ->lockForUpdate()
                ->get()
                ->keyBy('round_number');

            for ($n = 2; $n <= 5; $n++) {
                if (in_array($n, $disabled, true)) {
                    continue;
                }
                $prev = $this->previousEnabledRound($n, $disabled);
                if ($prev === null || ($rows->get($prev)->status ?? null) !== 'passed') {
                    continue;
                }
                $row = $rows->get($n);
                if (!$row) {
                    $row = $this->getOrCreateRoundProgressLocked($userId, $lessonId, $n, 'unlocked');
                    $rows->put($n, $row);
                }
                if ($row->status === 'locked') {
                    $row->status = 'unlocked';
                    $row->save();
                }
            }
        });
    }

    // GET /api/game/daily-word
    public function daily_word()
    {
        // Same word for everyone for the whole (Dhaka) day - it used to be a
        // fresh random pick on every request, so the "Word of the Day" card
        // changed each time the Home tab was opened.
        $word = $this->wordForDay(Word::where('status', 1), now('Asia/Dhaka')->toDateString());

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
    // round: 1=MCQ, 2=Reading, 3=Picture, 4=Listening, 5=Writing
    public function roundQuiz($lesson_id, $round, Request $request, QuizQuestionBuilder $builder)
    {
        $clientRound = (int) $round;
        $round = $this->toCanonicalRound($request, $clientRound);
        if ($round === null) {
            return response()->json(['status' => 'error', 'message' => 'Invalid round'], 422);
        }
        $disabled = $this->disabledRounds($request);
        if (in_array($round, $disabled, true)) {
            return response()->json(['status' => 'error', 'message' => 'এই রাউন্ড শীঘ্রই আসছে'], 404);
        }

        $lesson = Lesson::find($lesson_id);
        if ($lesson && $lesson->is_premium && !$this->isLessonUnlockedByRequest($request, $lesson_id)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'এই লেসন Premium — আগে আনলক করো',
            ], 403);
        }

        // Resolved for every round (not just round>1) - round 1 is reachable
        // by guests (bearer optional, may be null), but a logged-in user's
        // id is also needed here to drive selectWordIdsForUser()'s anti-repeat
        // rotation below, not just the sequential-unlock check.
        $user = $this->resolveUserFromBearer($request);

        // Sequential round-unlock was previously enforced only by the
        // Android level-map UI graying out locked rounds - a direct API
        // call could fetch (and via submitRound(), pass+reward) round 5
        // without ever touching 1-4. Round 1 has no prerequisite.
        if ($round > 1) {
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'লগইন করো আগে'], 401);
            }
            $this->healRoundUnlocks($user->id, (int) $lesson_id, $disabled);
            $progress = UserLessonRoundProgress::where('user_id', $user->id)
                ->where('lesson_id', $lesson_id)
                ->where('round_number', $round)
                ->first();
            if (!$progress || $progress->status === 'locked') {
                return response()->json(['status' => 'error', 'message' => 'আগের রাউন্ড ক্লিয়ার করো আগে'], 403);
            }
        }

        $questions = match ($round) {
            1 => $builder->buildQuestionsFromWordIds($builder->selectWordIdsForUser($user?->id, $lesson_id, 10)),
            2 => $builder->buildReadingQuestions($user?->id, $lesson_id, 12),
            3 => $builder->buildMatchQuestions($user?->id, $lesson_id, 10),
            4 => $builder->buildListeningQuestions($user?->id, $lesson_id, 10),
            5 => $builder->buildWritingQuestions($user?->id, $lesson_id, 10),
        };

        if (empty($questions)) {
            return response()->json(['status' => 'error', 'message' => 'No words found for this lesson'], 404);
        }

        // Record exposure so the next call for this user rotates through
        // the rest of the lesson instead of re-drawing the same words.
        $builder->markWordsShown($user?->id, collect($questions)->pluck('word_id')->filter()->values()->toArray());

        return response()->json([
            'status' => 'success',
            'data'   => [
                'round'      => $clientRound,
                'max_hearts' => 3,
                'questions'  => $questions,
            ],
        ]);
    }

    // GET /api/app/game/level-map/{lesson_id}  (auth required)
    public function levelMap(Request $request, $lesson_id)
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

        $disabled = $this->disabledRounds($request);
        $this->healRoundUnlocks($user->id, (int) $lesson_id, $disabled);

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

        $entry = function (int $n) use ($rows, $disabled) {
            if (in_array($n, $disabled, true)) {
                return ['status' => 'soon', 'stars' => 0];
            }
            $row = $rows->get($n);
            return ['status' => $row->status ?? 'locked', 'stars' => $row->stars ?? 0];
        };

        if ($this->isCurrentClient($request)) {
            $rounds = collect(range(1, 5))->map(fn ($n) => ['round' => $n] + $entry($n));
        } else {
            // legacy numbering: 4 rounds, no "soon" entries
            $rounds = collect(self::LEGACY_ROUND_MAP)->map(
                fn ($canonical, $clientRound) => ['round' => $clientRound] + $entry($canonical)
            )->values();
        }

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
            'round'       => 'required|integer|min:1|max:5',
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
        $round  = $this->toCanonicalRound($request, (int) $request->round);
        $disabled = $this->disabledRounds($request);
        if ($round === null || in_array($round, $disabled, true)) {
            return response()->json(['status' => 'error', 'message' => 'অবৈধ রাউন্ড'], 422);
        }
        $passed = $request->hearts_lost < 3;
        $this->healRoundUnlocks($user->id, (int) $request->lesson_id, $disabled);

        $result = DB::transaction(function () use ($user, $request, $round, $passed, $disabled) {
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

                $next = $this->nextEnabledRound($round, $disabled);
                if ($next !== null) {
                    $nextRound = $this->getOrCreateRoundProgressLocked(
                        $user->id, $request->lesson_id, $next, 'locked'
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
                // Same formula as streak()/add_xp() - lets the client refresh
                // its cached rank right after a round instead of showing a
                // stale one until the next Home visit.
                'rank'               => User::where('points', '>', $user->points ?? 0)->count() + 1,
            ],
        ]);
    }

    // Same day/yesterday logic as update_streak() below, factored out so
    // submitRound() can update the streak in the same atomic call instead
    // of relying on a separate client-fired POST.
    private function bumpStreak(User $user): int
    {
        $today      = $this->dhakaDate(now());
        $lastPlayed = $user->last_played_at ? $this->dhakaDate($user->last_played_at) : null;

        if ($lastPlayed === $today) {
            return $user->streak_days ?? 0;
        }

        $yesterday = $this->dhakaDate(now()->subDay());
        $newStreak = ($lastPlayed === $yesterday) ? (($user->streak_days ?? 0) + 1) : 1;

        // forceFill: streak_days / last_played_at are not in User::$fillable,
        // so the plain update([...]) this used to be silently dropped every
        // attribute - the response said "streak 1" but nothing was ever
        // stored (streak stayed 0, last_played_at stayed NULL for everyone).
        $user->forceFill(['streak_days' => $newStreak, 'last_played_at' => now()])->save();

        return $newStreak;
    }

    // Calendar day in Dhaka for a timestamp - the same day boundary the daily
    // Lipto cap already uses, so "played today" means the user's own day, not
    // the UTC day that flips at 6 AM in Bangladesh.
    private function dhakaDate($moment): string
    {
        return \Illuminate\Support\Carbon::parse($moment)->setTimezone('Asia/Dhaka')->toDateString();
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

        // Same one-way tier ladder LiptoController::earn() maintains - without
        // this a mystery-box win never raised lipto_max_balance, so spending
        // afterwards demoted the tier badge below what the user had reached.
        DB::table('users')->where('id', $locked->id)->update([
            'lipto_max_balance' => DB::raw('GREATEST(lipto_max_balance, ' . (int) $locked->lipto_balance . ')'),
        ]);

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
        // Competition ranking (ties share a rank) so a row here matches the
        // rank streak()/add_xp() report for the same user - those count
        // "users with more points + 1", which is what a plain index+1 broke
        // for anyone tied with others (Profile #27 vs Leaderboard #23).
        // id is the tiebreaker so the order of tied rows is stable.
        $prevPoints = null;
        $prevRank   = 0;
        $users = User::select('id', 'name', 'points')
            ->orderByDesc('points')
            ->orderBy('id')
            ->limit(50)
            ->get()
            ->map(function ($u, $index) use (&$prevPoints, &$prevRank) {
                $points = (int) $u->points;
                $rank   = ($prevPoints !== null && $points === $prevPoints) ? $prevRank : $index + 1;
                $prevPoints = $points;
                $prevRank   = $rank;

                return [
                    'id'     => $u->id,
                    'rank'   => $rank,
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
        $alreadyUpdatedToday = $user->last_played_at
            && $this->dhakaDate($user->last_played_at) === $this->dhakaDate(now());

        $streakDays = $this->bumpStreak($user);

        return response()->json([
            'status' => 'success',
            'data'   => $alreadyUpdatedToday
                ? ['streak_days' => $streakDays, 'message' => 'already_updated']
                : ['streak_days' => $streakDays],
        ]);
    }

    // Picks one row for the given calendar day without ORDER BY RAND() (full
    // table scan+sort on a large table). The day string is hashed into the
    // id range so the pick is stable within a day and different across days;
    // crc32() is used instead of mt_srand() so the global RNG that
    // rollMysteryBox() relies on is never reseeded.
    private function wordForDay($query, string $day)
    {
        $bounds = (clone $query)->selectRaw('MIN(id) as min_id, MAX(id) as max_id')->first();
        if (!$bounds || $bounds->min_id === null) {
            return null;
        }
        $span     = (int) $bounds->max_id - (int) $bounds->min_id + 1;
        $targetId = (int) $bounds->min_id + ((crc32($day) & 0x7fffffff) % $span);
        return (clone $query)->where('id', '>=', $targetId)->orderBy('id')->first()
            ?? (clone $query)->orderBy('id')->first();
    }
}
