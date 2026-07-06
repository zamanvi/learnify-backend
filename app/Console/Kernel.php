<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // সকাল ৭:৩০ — daily word notification (Bangladesh = UTC+6, so 01:30 UTC)
        $schedule->command('notify:morning-word')->dailyAt('01:30');

        // রাত ৮:০০ — streak reminder (Bangladesh = UTC+6, so 14:00 UTC)
        $schedule->command('notify:evening-streak')->dailyAt('14:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
