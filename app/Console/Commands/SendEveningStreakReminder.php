<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\FcmService;
use Illuminate\Console\Command;

class SendEveningStreakReminder extends Command
{
    protected $signature   = 'notify:evening-streak';
    protected $description = 'Send evening streak reminder to users who have not played today';

    public function handle(): void
    {
        $fcm      = new FcmService();
        $today    = now()->toDateString();
        $count    = 0;

        $messages = [
            "🔥 তোমার streak এখনো বেঁচে আছে! আজকের quiz দিয়ে ধরে রাখো।",
            "⚡ মাত্র ২ মিনিট! আজকের quiz দিয়ে streak বাঁচাও।",
            "💪 আজ হাল ছাড়ো না — quiz দাও।",
            "🏆 তোমার leaderboard rank কমে যাচ্ছে! Quiz দিয়ে rank ধরে রাখো।",
            "📚 প্রতিদিন একটু একটু শেখাই আসল শেখা। আজকের quiz বাকি!",
        ];
        $msg = $messages[date('N') % count($messages)]; // rotate by weekday

        // Only send to users who have NOT played today
        User::whereNotNull('device_token')
            ->where(function ($q) use ($today) {
                $q->whereNull('last_played_at')
                  ->orWhereDate('last_played_at', '<', $today);
            })
            ->select('device_token')
            ->chunk(100, function ($users) use ($fcm, $msg, &$count) {
                foreach ($users as $user) {
                    try {
                        $fcm->sendToDevice(
                            $user->device_token,
                            "ইংরেজি শিখছ তুমি 🔥",
                            $msg,
                            ['type' => 'streak_reminder']
                        );
                        $count++;
                    } catch (\Exception $e) { /* skip */ }
                }
            });

        \App\Models\NotificationLog::create([
            'type'             => 'streak_reminder',
            'title'            => 'ইংরেজি শিখছ তুমি 🔥',
            'body'             => $msg,
            'recipients_count' => $count,
            'sent_at'          => now(),
        ]);

        $this->info("Evening streak reminder sent to {$count} users.");
    }
}
