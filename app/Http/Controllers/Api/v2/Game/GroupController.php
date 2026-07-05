<?php

namespace App\Http\Controllers\Api\v2\Game;

use App\Http\Controllers\Controller;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GroupController extends Controller
{
    // POST /api/app/game/group/create
    // body: { name: string }
    public function create(Request $request)
    {
        $request->validate(['name' => 'required|string|max:50']);
        $user = $request->user();

        // Generate unique 8-char code
        do {
            $code = strtoupper(Str::random(8));
        } while (Group::where('code', $code)->exists());

        $group = Group::create([
            'created_by' => $user->id,
            'name'       => $request->name,
            'code'       => $code,
        ]);

        // Creator is also a member
        $group->members()->attach($user->id);

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

        $group = Group::where('code', strtoupper($request->code))
                      ->where('is_active', true)
                      ->first();

        if (!$group) {
            return response()->json(['status' => 'error', 'message' => 'Invalid code'], 404);
        }

        // Already a member?
        if ($group->members()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'status' => 'success',
                'group'  => $this->formatGroup($group, $user->id),
            ]);
        }

        $group->members()->attach($user->id);

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
                       ->with(['members' => fn($q) => $q->select('users.id', 'users.name', 'users.points')])
                       ->get();

        return response()->json([
            'status' => 'success',
            'groups' => $groups->map(fn($g) => $this->formatGroup($g, $user->id)),
        ]);
    }

    // GET /api/app/game/group/{code}/leaderboard
    public function leaderboard(Request $request, string $code)
    {
        $user  = $request->user();
        $group = Group::where('code', strtoupper($code))->first();

        if (!$group) {
            return response()->json(['status' => 'error', 'message' => 'Group not found'], 404);
        }

        // Verify user is a member
        if (!$group->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Not a member'], 403);
        }

        $members = $group->members()
            ->select('users.id', 'users.name', 'users.points')
            ->orderByDesc('users.points')
            ->get()
            ->map(function ($u, $idx) use ($user) {
                return [
                    'rank'     => $idx + 1,
                    'id'       => $u->id,
                    'name'     => $u->name,
                    'xp'       => (int) $u->points,
                    'is_me'    => $u->id === $user->id,
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
        $group = Group::where('code', strtoupper($code))->first();

        if (!$group) {
            return response()->json(['status' => 'error', 'message' => 'Group not found'], 404);
        }

        $group->members()->detach($user->id);

        return response()->json(['status' => 'success', 'message' => 'Left group']);
    }

    private function formatGroup(Group $group, int $myUserId): array
    {
        $memberCount = $group->members()->count();
        return [
            'id'           => $group->id,
            'name'         => $group->name,
            'code'         => $group->code,
            'member_count' => $memberCount,
            'is_creator'   => $group->created_by === $myUserId,
        ];
    }
}
