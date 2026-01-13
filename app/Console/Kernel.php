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

        // Cleanup soft deleted users older than 30 days (every month)
        $schedule->command('users:cleanup-soft-deleted --days=30')->monthly();

        // Cleanup unverified users older than 7 days (daily)
        $schedule->command('users:cleanup-unverified')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }


}
