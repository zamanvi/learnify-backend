<?php

namespace App\Http\Controllers\Api\v2\Game;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LiptoTransaction;
use App\Models\UnlockedLesson;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PremiumController extends Controller
{
    const UNLOCK_COST = 50;

    // POST /api/app/game/premium/lesson/{id}/unlock
    public function unlockLesson(Request $request, $id)
    {
        $user   = $request->user();
        $lesson = Lesson::find($id);

        if (!$lesson || !$lesson->is_premium) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This lesson is not premium',
            ], 422);
        }

        if (UnlockedLesson::where('user_id', $user->id)->where('lesson_id', $id)->exists()) {
            return response()->json([
                'status'          => 'success',
                'already_unlocked'=> true,
                'balance'         => (int) $user->lipto_balance,
            ]);
        }

        $result = DB::transaction(function () use ($user, $lesson) {
            // Lock this user's row so a double-tap / concurrent unlock request
            // can't both pass the balance check and double-spend.
            $locked = User::whereKey($user->id)->lockForUpdate()->first();

            if ($locked->lipto_balance < self::UNLOCK_COST) {
                return ['ok' => false, 'balance' => (int) $locked->lipto_balance];
            }

            $locked->decrement('lipto_balance', self::UNLOCK_COST);

            LiptoTransaction::create([
                'user_id'       => $locked->id,
                'amount'        => -self::UNLOCK_COST,
                'type'          => 'spend',
                'source'        => 'premium_unlock',
                'description'   => 'Unlocked premium lesson: ' . $lesson->title,
                'balance_after' => $locked->lipto_balance,
            ]);

            UnlockedLesson::create([
                'user_id'   => $locked->id,
                'lesson_id' => $lesson->id,
            ]);

            return ['ok' => true, 'balance' => (int) $locked->lipto_balance];
        });

        if (!$result['ok']) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Insufficient Lipto balance',
                'balance' => $result['balance'],
            ], 422);
        }

        return response()->json([
            'status'   => 'success',
            'unlocked' => true,
            'balance'  => $result['balance'],
        ]);
    }
}
