<?php

namespace App\Http\Controllers\Api\v2\Game;

use App\Http\Controllers\Controller;
use App\Services\FcmService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // POST /api/app/game/notification/token
    // body: { token: string }
    public function saveToken(Request $request)
    {
        $request->validate(['token' => 'required|string']);
        $request->user()->update(['device_token' => $request->token]);

        return response()->json(['status' => 'success']);
    }

    // POST /api/app/game/notification/battle
    // Sends a push to the opponent when challenged
    // Called internally by BattleController after challenge() — not exposed to client
    public static function sendBattleChallenge(string $opponentToken, string $challengerName, int $battleId): void
    {
        if (empty($opponentToken)) return;

        try {
            $fcm = new FcmService();
            $fcm->sendToDevice(
                $opponentToken,
                "⚔️ চ্যালেঞ্জ পেয়েছো!",
                "{$challengerName} তোমাকে 1v1 battle এ ডেকেছে! এখনই খেলো।",
                ['type' => 'battle', 'battle_id' => (string) $battleId]
            );
        } catch (\Exception $e) {
            // Notification failure is non-critical
        }
    }

    // POST /api/app/game/notification/test  (dev only)
    public function test(Request $request)
    {
        $user = $request->user();
        if (empty($user->device_token)) {
            return response()->json(['status' => 'error', 'message' => 'No device token saved'], 422);
        }

        try {
            $fcm = new FcmService();
            $fcm->sendToDevice(
                $user->device_token,
                "🧪 Test Notification",
                "Learnify notification কাজ করছে!",
                ['type' => 'test']
            );
            return response()->json(['status' => 'success', 'message' => 'Sent']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
