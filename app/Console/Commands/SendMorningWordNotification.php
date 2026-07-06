<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\FcmService;
use Illuminate\Console\Command;

class SendMorningWordNotification extends Command
{
    protected $signature   = 'notify:morning-word';
    protected $description = 'Send daily morning word notification to all users with a device token';

    public function handle(): void
    {
        // Get today's daily word from game API logic
        $word = \App\Models\DailyWord::whereDate('date', today())->first();
        $wordText = $word ? $word->word : 'Perseverance';

        $fcm   = new FcmService();
        $count = 0;

        User::whereNotNull('device_token')
            ->select('device_token')
            ->chunk(100, function ($users) use ($fcm, $wordText, &$count) {
                foreach ($users as $user) {
                    try {
                        $fcm->sendToDevice(
                            $user->device_token,
                            "📚 আজকের Word: {$wordText}",
                            "প্রতিদিন একটু একটু শেখো। আজকের quiz বাকি! 🔥",
                            ['type' => 'morning_word', 'word' => $wordText]
                        );
                        $count++;
                    } catch (\Exception $e) { /* skip failed tokens */ }
                }
            });

        \App\Models\NotificationLog::create([
            'type'             => 'morning_word',
            'title'            => "📚 আজকের Word: {$wordText}",
            'body'             => 'প্রতিদিন একটু একটু শেখো। আজকের quiz বাকি! 🔥',
            'recipients_count' => $count,
            'sent_at'          => now(),
        ]);

        $this->info("Morning word notification sent to {$count} users.");
    }
}
