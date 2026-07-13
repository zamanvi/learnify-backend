<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    // Existing (pre-friend_code) users would otherwise never get one, since
    // it's only assigned at registration time going forward.
    public function up(): void
    {
        User::whereNull('friend_code')->orderBy('id')->chunkById(200, function ($users) {
            foreach ($users as $user) {
                $user->update(['friend_code' => User::generateFriendCode()]);
            }
        });
    }

    public function down(): void
    {
        // Not reversible - backfilled values are additive and not derived
        // from anything worth restoring to null.
    }
};
