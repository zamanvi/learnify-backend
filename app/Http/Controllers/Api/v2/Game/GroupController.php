<?php

namespace App\Http\Controllers\Api\v2\Game;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class GroupController extends Controller
{
    // Most groups one person may create - stops a script / mis-tap from
    // filling the table with throw-away groups.
    private const MAX_CREATED_GROUPS = 5;

    // Characters without look-alikes (no 0/O, 1/I/L) so a code read out in a
    // class or typed from a WhatsApp message is not mistyped.
    private const CODE_CHARS = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    // POST /api/app/game/group/create
    // body: { name: string }
    public function create(Request $request)
    {
        $request->validate(['name' => 'required|string|max:50']);
        $user = $request->user();
        $name = trim((string) $request->name);

        if ($name === '') {
            return response()->json(['status' => 'error', 'message' => 'গ্রুপের নাম দাও'], 422);
        }

        $created = Group::where('created_by', $user->id)->where('is_active', true)->count();
        if ($created >= self::MAX_CREATED_GROUPS) {
            return response()->json([
                'status'  => 'error',
                'message' => 'একজন সর্বোচ্চ ' . self::MAX_CREATED_GROUPS . 'টি গ্রুপ তৈরি করতে পারে। আগেরটা ছেড়ে দিয়ে আবার চেষ্টা করো।',
            ], 422);
        }

        // Generate unique 8-char code
        do {
            $code = $this->makeCode();
        } while (Group::where('code', $code)->exists());

        $group = Group::create([
            'created_by' => $user->id,
            'name'       => $name,
            'code'       => $code,
        ]);

        // Creator is also a member
        $group->members()->attach($user->id, $this->joinPivot($user));

        return response()->json([
            'status' => 'success',
            'group'  => $this->formatGroup($group, $user->id),
        ]);
    }

    // POST /api/app/game/group/join
    // body: { code: string }
    public function join(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $user = $request->user();

        $group = Group::where('code', $this->normalizeCode($request->code))
                      ->where('is_active', true)
                      ->first();

        if (!$group) {
            return response()->json(['status' => 'error', 'message' => 'কোডটা ঠিক নেই — আবার দেখে লেখো'], 404);
        }

        // Already a member?
        if ($group->members()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'status' => 'success',
                'group'  => $this->formatGroup($group, $user->id),
            ]);
        }

        $group->members()->attach($user->id, $this->joinPivot($user));

        return response()->json([
            'status'  => 'success',
            'message' => 'Joined group',
            'group'   => $this->formatGroup($group, $user->id),
        ]);
    }

    // GET /api/app/game/group/my
    public function myGroups(Request $request)
    {
        $user = $request->user();

        $groups = Group::whereHas('members', fn($q) => $q->where('user_id', $user->id))
                       ->where('is_active', true)
                       ->with(['members' => fn($q) => $q->select('users.id', 'users.name', 'users.points')])
                       ->get();

        return response()->json([
            'status' => 'success',
            'groups' => $groups->map(fn($g) => $this->formatGroup($g, $user->id)),
        ]);
    }

    // GET /api/app/game/group/{code}/leaderboard
    // "xp" is the XP a member earned since joining this group, so a class of
    // beginners and a newcomer with a big lifetime total start level.
    public function leaderboard(Request $request, string $code)
    {
        $user  = $request->user();
        $group = Group::where('code', $this->normalizeCode($code))->first();

        if (!$group) {
            return response()->json(['status' => 'error', 'message' => 'গ্রুপ খুঁজে পাওয়া যায়নি'], 404);
        }

        // Verify user is a member
        if (!$group->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'তুমি এই গ্রুপের সদস্য নও'], 403);
        }

        $rows = $group->members()
            ->select('users.id', 'users.name', 'users.points')
            ->get()
            ->map(function ($u) {
                $gained = max(0, (int) $u->points - (int) ($u->pivot->points_at_join ?? 0));
                return ['id' => $u->id, 'name' => $u->name, 'xp' => $gained];
            })
            ->sort(function ($a, $b) {
                // most XP first, ties alphabetical so the order never flickers
                return [$b['xp'], $a['name']] <=> [$a['xp'], $b['name']];
            })
            ->values();

        // Equal XP shares a rank ("1, 1, 3"), like the global leaderboard.
        $prevXp   = null;
        $prevRank = 0;
        $members  = $rows->map(function ($row, $idx) use ($user, &$prevXp, &$prevRank) {
            $rank = ($prevXp !== null && $row['xp'] === $prevXp) ? $prevRank : $idx + 1;
            $prevXp   = $row['xp'];
            $prevRank = $rank;
            return [
                'rank'  => $rank,
                'id'    => $row['id'],
                'name'  => $row['name'],
                'xp'    => $row['xp'],
                'is_me' => $row['id'] === $user->id,
            ];
        });

        return response()->json([
            'status'  => 'success',
            'group'   => ['name' => $group->name, 'code' => $group->code],
            'members' => $members,
        ]);
    }

    // DELETE /api/app/game/group/{code}/leave
    public function leave(Request $request, string $code)
    {
        $user  = $request->user();
        $group = Group::where('code', $this->normalizeCode($code))->first();

        if (!$group) {
            return response()->json(['status' => 'error', 'message' => 'গ্রুপ খুঁজে পাওয়া যায়নি'], 404);
        }

        $group->members()->detach($user->id);

        // Nobody left: retire the group so it stops counting towards the
        // creator's limit and its code can no longer be joined.
        if (!$group->members()->exists()) {
            $group->update(['is_active' => false]);
        }

        return response()->json(['status' => 'success', 'message' => 'Left group']);
    }

    private function normalizeCode(string $code): string
    {
        return strtoupper(preg_replace('/[\s-]+/', '', $code));
    }

    private function makeCode(): string
    {
        $max  = strlen(self::CODE_CHARS) - 1;
        $code = '';
        for ($i = 0; $i < 8; $i++) {
            $code .= self::CODE_CHARS[random_int(0, $max)];
        }
        return $code;
    }

    // XP balance remembered at join time (see leaderboard()). Skipped when
    // the column is not there yet so create/join never fail on a half-done deploy.
    private function joinPivot($user): array
    {
        if (!Schema::hasColumn('group_members', 'points_at_join')) {
            return [];
        }
        return ['points_at_join' => max(0, (int) ($user->points ?? 0))];
    }

    private function formatGroup(Group $group, int $myUserId): array
    {
        // Use the already eager-loaded relation when available (e.g. myGroups())
        // instead of firing a fresh COUNT query per group.
        $memberCount = $group->relationLoaded('members')
            ? $group->members->count()
            : $group->members()->count();
        return [
            'id'           => $group->id,
            'name'         => $group->name,
            'code'         => $group->code,
            'member_count' => $memberCount,
            'is_creator'   => $group->created_by === $myUserId,
        ];
    }
}
