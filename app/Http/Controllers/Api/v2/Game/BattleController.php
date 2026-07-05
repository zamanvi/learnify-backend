<?php

namespace App\Http\Controllers\Api\v2\Game;

use App\Http\Controllers\Controller;
use App\Models\Battle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        return response()->json([
            'status'    => 'success',
            'battle_id' => $battle->id,
            'lesson_id' => $battle->lesson_id,
            'opponent'  => $this->formatUser(User::find($opponentId)),
        ]);
    }

    // POST /api/app/game/battle/{id}/submit
    // body: { score: int, total: int, time_sec: int }
    public function submit(Request $request, int $id)
    {
        $request->validate([
            'score'    => 'required|integer|min:0',
            'total'    => 'required|integer|min:1',
            'time_sec' => 'required|integer|min:0',
        ]);

        $user   = $request->user();
        $battle = Battle::findOrFail($id);

        if (!in_array($battle->status, ['pending', 'challenger_done'])) {
            return response()->json(['status' => 'error', 'message' => 'Battle already completed'], 422);
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

        if ($battle->challenger_id !== $user->id && $battle->opponent_id !== $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Not a participant'], 403);
        }

        return response()->json([
            'status' => 'success',
            'battle' => $this->formatBattle($battle, $user->id),
        ]);
    }

    // GET /api/app/game/battle/pending
    // Battles where I'm the opponent and haven't played yet
    public function pending(Request $request)
    {
        $user = $request->user();

        $battles = Battle::where('opponent_id', $user->id)
            ->where('status', 'challenger_done')
            ->whereNull('opponent_score')
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

        $battles = Battle::where(function ($q) use ($user) {
            $q->where('challenger_id', $user->id)->orWhere('opponent_id', $user->id);
        })->where('status', 'completed')
          ->with(['challenger:id,name', 'opponent:id,name'])
          ->latest()
          ->limit(20)
          ->get();

        return response()->json([
            'status'   => 'success',
            'battles'  => $battles->map(fn($b) => $this->formatBattle($b, $user->id)),
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────

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
            'challenger' => $this->formatUser($battle->challenger),
            'opponent'   => $this->formatUser($battle->opponent),
        ];
    }

    private function formatUser(?User $user): ?array
    {
        if (!$user) return null;
        return ['id' => $user->id, 'name' => $user->name, 'xp' => (int) $user->points];
    }
}
