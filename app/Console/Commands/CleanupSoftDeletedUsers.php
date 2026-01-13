<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CleanupSoftDeletedUsers extends Command
{
    protected $signature = 'users:cleanup-soft-deleted {--days=30}';
    protected $description = 'Permanently delete soft-deleted users older than X days';

    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = now()->subDays($days);

        // Force delete soft deleted users older than X days
        $deleted = User::onlyTrashed()
            ->where('deleted_at', '<', $cutoffDate)
            ->forceDelete();

        $this->info("Permanently deleted {$deleted} soft-deleted users older than {$days} days.");

        return 0;
    }
}
