<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanupUnverifiedUsers extends Command
{
    protected $signature = 'users:cleanup-unverified';
    protected $description = 'Delete unverified user accounts older than 7 days';

    public function handle()
    {
        $cutoffDate = now()->subDays(7);

        $deleted = User::whereNull('email_verified_at')
            ->where('created_at', '<', $cutoffDate)
            ->where('status', 'active') // Hanya user biasa, teknisi pending biarin admin handle
            ->delete();

        $this->info("Cleaned up {$deleted} unverified user accounts older than 7 days.");

        return 0;
    }
}
