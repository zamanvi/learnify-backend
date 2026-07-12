<?php

namespace App\Http\Controllers\Api\v2\Game;

use App\Http\Controllers\Controller;
use App\Models\LiptoTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LiptoController extends Controller
{
    // GET /api/v2/game/lipto/balance
    public function balance(Request $request)
    {
        $user = $request->user();

        $recent = LiptoTransaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get(['id', 'amount', 'type', 'source', 'description', 'balance_after', 'created_at']);

        return response()->json([
            'status'  => 'success',
            'balance' => (int) $user->lipto_balance,
            'recent'  => $recent,
        ]);
    }

    // POST /api/v2/game/lipto/earn
    // body: { amount: int, source: string, description: string }
    public function earn(Request $request)
    {
        $request->validate([
            'amount'      => 'required|integer|min:1|max:1000',
            'source'      => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        $user   = $request->user();
        $amount = (int) $request->amount;

        DB::transaction(function () use ($user, $amount, $request) {
            $user->increment('lipto_balance', $amount);

            LiptoTransaction::create([
                'user_id'       => $user->id,
                'amount'        => $amount,
                'type'          => 'earn',
                'source'        => $request->source,
                'description'   => $request->description,
                'balance_after' => $user->lipto_balance,
            ]);
        });

        return response()->json([
            'status'  => 'success',
            'earned'  => $amount,
            'balance' => (int) $user->lipto_balance,
        ]);
    }

    // POST /api/v2/game/lipto/spend
    // body: { amount: int, source: string, description: string }
    public function spend(Request $request)
    {
        $request->validate([
            'amount'      => 'required|integer|min:1',
            'source'      => 'required|string|max:50',
            'description' => 'nullable|string|max:255',
        ]);

        $user   = $request->user();
        $amount = (int) $request->amount;

        if ($user->lipto_balance < $amount) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Insufficient Lipto balance',
                'balance' => (int) $user->lipto_balance,
            ], 422);
        }

        DB::transaction(function () use ($user, $amount, $request) {
            $user->decrement('lipto_balance', $amount);

            LiptoTransaction::create([
                'user_id'       => $user->id,
                'amount'        => -$amount,
                'type'          => 'spend',
                'source'        => $request->source,
                'description'   => $request->description,
                'balance_after' => $user->lipto_balance,
            ]);
        });

        return response()->json([
            'status'  => 'success',
            'spent'   => $amount,
            'balance' => (int) $user->lipto_balance,
        ]);
    }

    // POST /api/v2/game/lipto/transfer
    // body: { to_user_id: int, amount: int, description: string }
    public function transfer(Request $request)
    {
        $request->validate([
            'to_user_id'  => 'required|integer|exists:users,id',
            'amount'      => 'required|integer|min:1',
            'description' => 'nullable|string|max:255',
        ]);

        $sender   = $request->user();
        $amount   = (int) $request->amount;
        $receiver = User::findOrFail($request->to_user_id);

        if ($sender->id === $receiver->id) {
            return response()->json(['status' => 'error', 'message' => 'Cannot transfer to yourself'], 422);
        }

        if ($sender->lipto_balance < $amount) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Insufficient Lipto balance',
                'balance' => (int) $sender->lipto_balance,
            ], 422);
        }

        DB::transaction(function () use ($sender, $receiver, $amount, $request) {
            $sender->decrement('lipto_balance', $amount);
            $receiver->increment('lipto_balance', $amount);

            LiptoTransaction::create([
                'user_id'         => $sender->id,
                'amount'          => -$amount,
                'type'            => 'transfer_out',
                'source'          => 'transfer',
                'description'     => $request->description ?? 'Transfer to ' . $receiver->name,
                'balance_after'   => $sender->lipto_balance,
                'related_user_id' => $receiver->id,
            ]);

            LiptoTransaction::create([
                'user_id'         => $receiver->id,
                'amount'          => $amount,
                'type'            => 'transfer_in',
                'source'          => 'transfer',
                'description'     => $request->description ?? 'Transfer from ' . $sender->name,
                'balance_after'   => $receiver->lipto_balance,
                'related_user_id' => $sender->id,
            ]);
        });

        return response()->json([
            'status'  => 'success',
            'sent'    => $amount,
            'balance' => (int) $sender->lipto_balance,
        ]);
    }
}
