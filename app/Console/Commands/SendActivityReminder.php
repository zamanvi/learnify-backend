<?php

namespace App\Console\Commands;

use App\Models\NotificationLog;
use App\Models\User;
use App\Services\FcmService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendActivityReminder extends Command
{
    protected $signature   = 'notify:activity-reminder';
    protected $description = 'Send a daily push notification to every user, with tone/copy based on how long since they last used the app';

    // "Last active" is read from Sanctum's own personal_access_tokens.last_used_at
    // (auto-updated by Sanctum on every authenticated request) rather than a new
    // users column + middleware - this avoids touching any existing auth/route
    // middleware and is already accurate for every authenticated API call.
    public function handle(): void
    {
        $fcm   = new FcmService();
        $now   = now();
        $count = ['month' => 0, 'week' => 0, 'day' => 0, 'active' => 0];

        $lastActiveSub = DB::table('personal_access_tokens')
            ->where('tokenable_type', User::class)
            ->selectRaw('tokenable_id, MAX(last_used_at) as last_active')
            ->groupBy('tokenable_id');

        User::whereNotNull('device_token')
            ->leftJoinSub($lastActiveSub, 'la', 'la.tokenable_id', '=', 'users.id')
            ->select('users.device_token', 'users.name', DB::raw('COALESCE(la.last_active, users.created_at) as last_active'))
            ->chunk(200, function ($users) use ($fcm, $now, &$count) {
                foreach ($users as $user) {
                    $lastActive = $user->last_active ? \Carbon\Carbon::parse($user->last_active) : null;
                    $daysAgo    = $lastActive ? $lastActive->diffInDays($now) : 999;

                    if ($daysAgo >= 30) {
                        $bucket = 'month';
                    } elseif ($daysAgo >= 7) {
                        $bucket = 'week';
                    } elseif ($daysAgo >= 1) {
                        $bucket = 'day';
                    } else {
                        $bucket = 'active';
                    }

                    [$title, $body] = $this->messageFor($bucket, $user->name);

                    try {
                        $fcm->sendToDevice($user->device_token, $title, $body, ['type' => 'activity_reminder']);
                        $count[$bucket]++;
                    } catch (\Exception $e) { /* skip */ }
                }
            });

        $totalSent = array_sum($count);

        NotificationLog::create([
            'type'             => 'activity_reminder',
            'title'            => 'Daily activity reminder',
            'body'             => "month={$count['month']}, week={$count['week']}, day={$count['day']}, active={$count['active']}",
            'recipients_count' => $totalSent,
            'sent_at'          => now(),
        ]);

        $this->info("Activity reminder sent to {$totalSent} users ({$count['month']} 30+d, {$count['week']} 7-29d, {$count['day']} ~1d, {$count['active']} active-24h).");
    }

    /**
     * @return array{0: string, 1: string} [title, body]
     */
    private function messageFor(string $bucket, ?string $name): array
    {
        $firstName = $name ? explode(' ', trim($name))[0] : '';
        $greeting  = $firstName ? "{$firstName}, " : '';

        switch ($bucket) {
            case 'month':
                return [
                    'তোমাকে অনেক মিস করছি 😢',
                    "{$greeting}তুমি প্রায় এক মাস ধরে আসোনি... তোমার শেখা words গুলোও বুঝি তোমাকে মিস করছে 💔 আজই একবার ফিরে এসো, তোমার জন্য অপেক্ষা করছি।",
                ];
            case 'week':
                return [
                    'তোমাকে মনে পড়ছে 🙂',
                    "{$greeting}এক সপ্তাহ হয়ে গেল তোমার শেখা হয়নি। মাত্র ৫ মিনিট সময় বের করে আজকের একটা lesson শেষ করো তো?",
                ];
            case 'day':
                return [
                    'তোমার streak অপেক্ষা করছে ⏰',
                    "{$greeting}গতকাল থেকে আসোনি! আজ একটা quiz দিয়ে তোমার progress চালু রাখো, একদম হেরে যেতে দিও না 🔥",
                ];
            case 'active':
            default:
                return [
                    'দারুণ চলছে তোমার শেখা! 🎉',
                    "{$greeting}প্রতিদিন শেখার এই habit-টা দুর্দান্ত! আজও একটা নতুন কিছু শিখে ফেলো 🚀",
                ];
        }
    }
}
