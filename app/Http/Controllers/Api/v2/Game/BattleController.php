<?php

namespace App\Http\Controllers\Api\v2\Game;

use App\Http\Controllers\Controller;
use App\Models\Battle;
use App\Models\BattleParticipant;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\FcmService;
use App\Services\QuizQuestionBuilder;

class BattleController extends Controller
{
    // POST /api/app/game/battle/challenge
    // body: { opponent_id: int, lesson_id: int }
    public function challenge(Request $request)
    {
        $request->validate([
            'opponent_id' => 'required|integer|exists:users,id',
            'lesson_id'   => 'required|integer',
        ]);

        $challenger = $request->user();
        $opponentId = (int) $request->opponent_id;

        if ($challenger->id === $opponentId) {
            return response()->json(['status' => 'error', 'message' => 'Cannot challenge yourself'], 422);
        }

        // Check for existing active battle between them
        $existing = Battle::where(function ($q) use ($challenger, $opponentId) {
            $q->where('challenger_id', $challenger->id)->where('opponent_id', $opponentId);
        })->orWhere(function ($q) use ($challenger, $opponentId) {
            $q->where('challenger_id', $opponentId)->where('opponent_id', $challenger->id);
        })->whereIn('status', ['pending', 'challenger_done'])->first();

        if ($existing) {
            return response()->json([
                'status' => 'error',
                'message' => 'A battle is already in progress',
                'battle_id' => $existing->id,
            ], 409);
        }

        $battle = Battle::create([
            'challenger_id' => $challenger->id,
            'opponent_id'   => $opponentId,
            'lesson_id'     => $request->lesson_id,
            'status'        => 'pending',
        ]);

        // Push notification to opponent
        $opponent = User::find($opponentId);
        if ($opponent && $opponent->device_token) {
            try {
                (new FcmService())->sendToDevice(
                    $opponent->device_token,
                    "⚔️ চ্যালেঞ্জ পেয়েছো!",
                    "{$challenger->name} তোমাকে 1v1 battle এ ডেকেছে!",
                    ['type' => 'battle', 'battle_id' => (string) $battle->id]
                );
            } catch (\Exception $e) { /* non-critical */ }
        }

        return response()->json([
            'status'    => 'success',
            'battle_id' => $battle->id,
            'lesson_id' => $battle->lesson_id,
            'opponent'  => $this->formatUser($opponent),
        ]);
    }

    // POST /api/app/game/battle/create
    // body: { invitee_ids?: int[], lesson_id, question_count?, lives?, mode?, allow_code_join? }
    // Creates a "customize" battle (1 or many invitees). Legacy challenge()
    // above is untouched - old app builds keep using that endpoint exactly
    // as before.
    public function create(Request $request, QuizQuestionBuilder $builder)
    {
        $request->validate([
            'invitee_ids'     => 'nullable|array',
            'invitee_ids.*'   => 'integer|exists:users,id',
            'lesson_id'       => 'required|integer',
            'question_count'  => 'nullable|integer|min:1|max:50',
            'lives'           => 'nullable|integer|min:1|max:50',
            'mode'            => 'nullable|string|in:async,live',
            'allow_code_join' => 'nullable|boolean',
        ]);

        $creator = $request->user();
        $mode    = $request->input('mode', 'async');

        if ($mode === 'live') {
            return response()->json(['status' => 'error', 'message' => 'লাইভ মোড শীঘ্রই আসছে 🚀'], 422);
        }

        $inviteeIds = collect($request->input('invitee_ids', []))
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id !== $creator->id)
            ->unique()
            ->values();

        $allowCodeJoin = (bool) $request->input('allow_code_join', false);
        $questionCount = (int) $request->input('question_count', 10);
        $lives         = $request->filled('lives') ? (int) $request->input('lives') : null;

        $wordIds = $builder->selectWordIds((int) $request->lesson_id, $questionCount);
        if (empty($wordIds)) {
            return response()->json(['status' => 'error', 'message' => 'No words found for this lesson'], 404);
        }

        $inviteCode = null;
        if ($allowCodeJoin || $inviteeIds->isEmpty()) {
            do {
                $inviteCode = strtoupper(Str::random(8));
            } while (Battle::where('invite_code', $inviteCode)->exists());
        }

        $battle = DB::transaction(function () use ($creator, $request, $mode, $questionCount, $lives, $wordIds, $inviteCode, $inviteeIds, $allowCodeJoin) {
            $battle = Battle::create([
                'challenger_id'      => $creator->id,
                'opponent_id'        => null,
                'lesson_id'          => $request->lesson_id,
                'status'             => 'pending',
                'mode'               => $mode,
                'question_count'     => $questionCount,
                'lives'              => $lives,
                'question_word_ids'  => $wordIds,
                'invite_code'        => $inviteCode,
                'max_participants'   => $allowCodeJoin ? null : ($inviteeIds->count() + 1),
            ]);

            BattleParticipant::create([
                'battle_id'       => $battle->id,
                'user_id'         => $creator->id,
                'is_creator'      => true,
                'status'          => 'joined',
                'joined_at'       => now(),
                'lives_remaining' => $lives,
            ]);

            foreach ($inviteeIds as $inviteeId) {
                BattleParticipant::create([
                    'battle_id'       => $battle->id,
                    'user_id'         => $inviteeId,
                    'is_creator'      => false,
                    'status'          => 'invited',
                    'lives_remaining' => $lives,
                ]);
            }

            return $battle;
        });

        if ($inviteeIds->isNotEmpty()) {
            $invitees = User::whereIn('id', $inviteeIds)->get();
            foreach ($invitees as $invitee) {
                if ($invitee->device_token) {
                    try {
                        (new FcmService())->sendToDevice(
                            $invitee->device_token,
                            "🎮 চ্যালেঞ্জ পেয়েছো!",
                            "{$creator->name} তোমাকে একটা battle এ ডেকেছে!",
                            ['type' => 'battle', 'battle_id' => (string) $battle->id]
                        );
                    } catch (\Exception $e) { /* non-critical */ }
                }
            }
        }

        return response()->json([
            'status'             => 'success',
            'battle_id'          => $battle->id,
            'invite_code'        => $battle->invite_code,
            'question_word_ids'  => $battle->question_word_ids,
            'question_count'     => $battle->question_count,
            'lives'              => $battle->lives,
            'mode'               => $battle->mode,
            'lesson_id'          => $battle->lesson_id,
        ]);
    }

    // GET /api/app/game/battle/resolve-code?code=
    // Preview-only, writes nothing - lets Android show a confirmation card
    // before the user commits to joining (same pattern as Lipto findFriend).
    public function resolveCode(Request $request)
    {
        $request->validate(['code' => 'required|string']);

        $battle = Battle::where('invite_code', strtoupper($request->code))->first();
        if (!$battle) {
            return response()->json(['status' => 'error', 'message' => 'কোড খুঁজে পাওয়া যায়নি'], 404);
        }
        if (!in_array($battle->status, ['pending', 'challenger_done'])) {
            return response()->json(['status' => 'error', 'message' => 'এই battle আর যোগ দেওয়া যাবে না'], 422);
        }

        $participantCount = $battle->battle_participants()->count();
        if ($battle->max_participants && $participantCount >= $battle->max_participants) {
            return response()->json(['status' => 'error', 'message' => 'এই battle পূর্ণ হয়ে গেছে'], 422);
        }

        $creator = User::find($battle->challenger_id);
        $lesson  = Lesson::find($battle->lesson_id);

        return response()->json([
            'status'            => 'success',
            'battle_id'         => $battle->id,
            'creator_name'      => $creator->name ?? null,
            'lesson_id'         => $battle->lesson_id,
            'lesson_title'      => $lesson->title ?? null,
            'question_count'    => $battle->question_count ?? 10,
            'lives'             => $battle->lives,
            'mode'              => $battle->mode,
            'participant_count' => $participantCount,
        ]);
    }

    // POST /api/app/game/battle/join
    // body: { code: string }
    public function join(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $user = $request->user();

        $battle = Battle::where('invite_code', strtoupper($request->code))->first();
        if (!$battle) {
            return response()->json(['status' => 'error', 'message' => 'কোড খুঁজে পাওয়া যায়নি'], 404);
        }
        if (!in_array($battle->status, ['pending', 'challenger_done'])) {
            return response()->json(['status' => 'error', 'message' => 'এই battle আর যোগ দেওয়া যাবে না'], 422);
        }
        if ($battle->challenger_id === $user->id) {
            return response()->json(['status' => 'error', 'message' => 'এটা তোমারই তৈরি battle'], 422);
        }

        $existing = BattleParticipant::where('battle_id', $battle->id)->where('user_id', $user->id)->first();
        if ($existing) {
            return response()->json(['status' => 'success', 'battle' => $this->formatBattle($battle->fresh(), $user->id)]);
        }

        $participantCount = $battle->battle_participants()->count();
        if ($battle->max_participants && $participantCount >= $battle->max_participants) {
            return response()->json(['status' => 'error', 'message' => 'এই battle পূর্ণ হয়ে গেছে'], 422);
        }

        BattleParticipant::create([
            'battle_id'       => $battle->id,
            'user_id'         => $user->id,
            'is_creator'      => false,
            'status'          => 'joined',
            'joined_at'       => now(),
            'lives_remaining' => $battle->lives,
        ]);

        $creator = User::find($battle->challenger_id);
        if ($creator && $creator->device_token) {
            try {
                (new FcmService())->sendToDevice(
                    $creator->device_token,
                    "🎉 নতুন খেলোয়াড় যোগ দিয়েছে!",
                    "{$user->name} তোমার battle এ যোগ দিয়েছে!",
                    ['type' => 'battle', 'battle_id' => (string) $battle->id]
                );
            } catch (\Exception $e) { /* non-critical */ }
        }

        return response()->json([
            'status' => 'success',
            'battle' => $this->formatBattle($battle->fresh(), $user->id),
        ]);
    }

    // POST /api/app/game/battle/{id}/submit
    // body: { score: int, total: int, time_sec: int }
    public function submit(Request $request, int $id)
    {
        $request->validate([
            'score'           => 'required|integer|min:0',
            'total'           => 'required|integer|min:1',
            'time_sec'        => 'required|integer|min:0',
            'lives_remaining' => 'nullable|integer|min:0',
        ]);

        $user   = $request->user();
        $battle = Battle::findOrFail($id);

        if (!in_array($battle->status, ['pending', 'challenger_done'])) {
            return response()->json(['status' => 'error', 'message' => 'Battle already completed'], 422);
        }

        if ($battle->battle_participants()->exists()) {
            return $this->submitParticipant($request, $battle, $user);
        }

        DB::transaction(function () use ($battle, $user, $request) {
            if ($battle->challenger_id === $user->id) {
                $battle->challenger_score    = $request->score;
                $battle->challenger_total    = $request->total;
                $battle->challenger_time_sec = $request->time_sec;
                $battle->status = $battle->opponent_score !== null ? 'completed' : 'challenger_done';
            } elseif ($battle->opponent_id === $user->id) {
                $battle->opponent_score    = $request->score;
                $battle->opponent_total    = $request->total;
                $battle->opponent_time_sec = $request->time_sec;
                $battle->status = $battle->challenger_score !== null ? 'completed' : 'challenger_done';
            } else {
                abort(403, 'Not a participant');
            }

            if ($battle->status === 'completed') {
                $battle->winner_id = $this->determineWinner($battle);
            }

            $battle->save();
        });

        $battle->refresh();

        return response()->json([
            'status'      => 'success',
            'battle'      => $this->formatBattle($battle, $user->id),
        ]);
    }

    // GET /api/app/game/battle/{id}
    public function show(Request $request, int $id)
    {
        $user   = $request->user();
        $battle = Battle::findOrFail($id);

        $isParticipant = $battle->challenger_id === $user->id
            || $battle->opponent_id === $user->id
            || $battle->battle_participants()->where('user_id', $user->id)->exists();

        if (!$isParticipant) {
            return response()->json(['status' => 'error', 'message' => 'Not a participant'], 403);
        }

        return response()->json([
            'status' => 'success',
            'battle' => $this->formatBattle($battle, $user->id),
        ]);
    }

    // GET /api/app/game/battle/pending
    // Battles where I'm the opponent and haven't played yet (legacy shape),
    // OR (new "customize" shape) I'm an invited/joined participant who
    // hasn't submitted yet.
    public function pending(Request $request)
    {
        $user = $request->user();

        $battles = Battle::where(function ($q) use ($user) {
                $q->where(function ($q2) use ($user) {
                    $q2->where('opponent_id', $user->id)
                       ->where('status', 'challenger_done')
                       ->whereNull('opponent_score');
                })->orWhereHas('battle_participants', function ($q2) use ($user) {
                    $q2->where('user_id', $user->id)->where('status', '!=', 'submitted');
                });
            })
            ->with(['challenger:id,name,points'])
            ->latest()
            ->get();

        return response()->json([
            'status'  => 'success',
            'battles' => $battles->map(fn($b) => $this->formatBattle($b, $user->id)),
        ]);
    }

    // GET /api/app/game/battle/history
    public function history(Request $request)
    {
        $user = $request->user();

        $battles = Battle::where('status', 'completed')
            ->where(function ($q) use ($user) {
                $q->where('challenger_id', $user->id)
                  ->orWhere('opponent_id', $user->id)
                  ->orWhereHas('battle_participants', fn($q2) => $q2->where('user_id', $user->id));
            })
            ->with(['challenger:id,name,points', 'opponent:id,name,points'])
            ->latest()
            ->limit(20)
            ->get();

        return response()->json([
            'status'   => 'success',
            'battles'  => $battles->map(fn($b) => $this->formatBattle($b, $user->id)),
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────

    private function submitParticipant(Request $request, Battle $battle, User $user)
    {
        $participant = BattleParticipant::where('battle_id', $battle->id)->where('user_id', $user->id)->first();
        if (!$participant) {
            abort(403, 'Not a participant');
        }

        if ($participant->status === 'submitted') {
            return response()->json(['status' => 'success', 'battle' => $this->formatBattle($battle, $user->id)]);
        }

        DB::transaction(function () use ($participant, $battle, $request) {
            $participant->score       = $request->score;
            $participant->total       = $request->total;
            $participant->time_sec    = $request->time_sec;
            $participant->status      = 'submitted';
            $participant->finished_at = now();
            if ($request->filled('lives_remaining')) {
                $participant->lives_remaining = $request->input('lives_remaining');
            }
            $participant->save();

            $allSubmitted = $battle->battle_participants()->where('status', '!=', 'submitted')->doesntExist();
            if ($allSubmitted) {
                $battle->status    = 'completed';
                $battle->winner_id = $this->determineWinnerN($battle);
                $battle->save();
            }
        });

        $battle->refresh();

        return response()->json([
            'status' => 'success',
            'battle' => $this->formatBattle($battle, $user->id),
        ]);
    }

    private function determineWinnerN(Battle $battle): ?int
    {
        $participants = $battle->battle_participants()->get();
        if ($participants->isEmpty()) return null;

        $maxScore   = $participants->max(fn($p) => $p->score ?? 0);
        $topByScore = $participants->filter(fn($p) => ($p->score ?? 0) === $maxScore);

        $minTime = $topByScore->min(fn($p) => $p->time_sec ?? PHP_INT_MAX);
        $winners = $topByScore->filter(fn($p) => ($p->time_sec ?? PHP_INT_MAX) === $minTime);

        return $winners->count() === 1 ? $winners->first()->user_id : null; // >1 = draw
    }

    private function determineWinner(Battle $battle): ?int
    {
        $cScore = $battle->challenger_score ?? 0;
        $oScore = $battle->opponent_score   ?? 0;

        if ($cScore > $oScore) return $battle->challenger_id;
        if ($oScore > $cScore) return $battle->opponent_id;

        // Tie-break by time (lower = better)
        $cTime = $battle->challenger_time_sec ?? PHP_INT_MAX;
        $oTime = $battle->opponent_time_sec   ?? PHP_INT_MAX;
        if ($cTime < $oTime) return $battle->challenger_id;
        if ($oTime < $cTime) return $battle->opponent_id;

        return null; // draw
    }

    private function formatBattle(Battle $battle, int $myId): array
    {
        if ($battle->battle_participants()->exists()) {
            return $this->formatBattleMulti($battle, $myId);
        }

        $isChallenger = $battle->challenger_id === $myId;
        $myScore    = $isChallenger ? $battle->challenger_score    : $battle->opponent_score;
        $theirScore = $isChallenger ? $battle->opponent_score      : $battle->challenger_score;

        $result = null;
        if ($battle->status === 'completed') {
            if ($battle->winner_id === null)   $result = 'draw';
            elseif ($battle->winner_id === $myId) $result = 'win';
            else $result = 'loss';
        }

        return [
            'id'         => $battle->id,
            'lesson_id'  => $battle->lesson_id,
            'status'     => $battle->status,
            'result'     => $result,
            'my_score'   => $myScore,
            'their_score'=> $theirScore,
            'winner_id'  => $battle->winner_id,
            // 'challenger'/'opponent' are raw DB roles, not "me" vs "them" -
            // clients that want the other player regardless of which role
            // I hold should use 'other' instead.
            'challenger' => $this->formatUser($battle->challenger),
            'opponent'   => $this->formatUser($battle->opponent),
            'other'      => $this->formatUser($isChallenger ? $battle->opponent : $battle->challenger),
        ];
    }

    private function formatBattleMulti(Battle $battle, int $myId): array
    {
        $participants = $battle->battle_participants()->with('user:id,name,points')->get();

        $result = null;
        if ($battle->status === 'completed') {
            if ($battle->winner_id === null)      $result = 'draw';
            elseif ($battle->winner_id === $myId) $result = 'win';
            else                                  $result = 'loss';
        }

        return [
            'id'              => $battle->id,
            'lesson_id'       => $battle->lesson_id,
            'status'          => $battle->status,
            'mode'            => $battle->mode,
            'question_count'  => $battle->question_count,
            'lives'           => $battle->lives,
            'invite_code'     => $battle->invite_code,
            'question_word_ids' => $battle->question_word_ids,
            'result'          => $result,
            'winner_id'       => $battle->winner_id,
            'participants'    => $participants->map(fn($p) => [
                'id'              => $p->user_id,
                'name'            => $p->user->name ?? null,
                'xp'              => (int) ($p->user->points ?? 0),
                'score'           => $p->score,
                'total'           => $p->total,
                'lives_remaining' => $p->lives_remaining,
                'status'          => $p->status,
                'is_me'           => $p->user_id === $myId,
                'is_creator'      => $p->is_creator,
            ])->values(),
        ];
    }

    private function formatUser(?User $user): ?array
    {
        if (!$user) return null;
        return ['id' => $user->id, 'name' => $user->name, 'xp' => (int) $user->points];
    }
}
