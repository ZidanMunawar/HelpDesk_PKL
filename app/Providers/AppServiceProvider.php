<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Di AppServiceProvider.php boot() method
use Illuminate\Support\Facades\Blade;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // public function boot(): void
    // {
    //     Blade::directive('statusicon', function ($status) {
    //         $icons = [
    //             'open' => 'folder-open',
    //             'received' => 'inbox',
    //             'pending_om' => 'clock',
    //             'in_progress' => 'spinner',
    //             'pending_vr' => 'file-invoice-dollar',
    //             'completed' => 'check-circle',
    //             'pending_gm' => 'user-tie',
    //             'closed' => 'times-circle',
    //             'cancelled' => 'ban'
    //         ];
    //         $status = strtolower(trim($status, "'\""));
    //         return $icons[$status] ?? 'circle';
    //     });

    //     Blade::directive('statusbadgecolor', function ($status) {
    //         $colors = [
    //             'open' => 'primary',
    //             'received' => 'info',
    //             'pending_om' => 'warning',
    //             'in_progress' => 'info',
    //             'pending_vr' => 'warning',
    //             'completed' => 'success',
    //             'pending_gm' => 'warning',
    //             'closed' => 'dark',
    //             'cancelled' => 'danger'
    //         ];
    //         $status = strtolower(trim($status, "'\""));
    //         return $colors[$status] ?? 'secondary';
    //     });
    // }
    public function boot()
    {
        // Helper untuk status icon
        Blade::directive('statusicon', function ($status) {
            $icons = [
                'open' => 'folder-open',
                'received' => 'inbox',
                'pending_om' => 'clock',
                'in_progress' => 'spinner',
                'pending_vr' => 'file-invoice-dollar',
                'completed' => 'check-circle',
                'pending_gm' => 'user-tie',
                'closed' => 'times-circle',
                'cancelled' => 'ban'
            ];
            $status = strtolower(trim($status, "'\""));
            return $icons[$status] ?? 'circle';
        });

        Blade::directive('statusbadgecolor', function ($status) {
            $colors = [
                'open' => 'primary',
                'received' => 'info',
                'pending_om' => 'warning',
                'in_progress' => 'info',
                'pending_vr' => 'warning',
                'completed' => 'success',
                'pending_gm' => 'warning',
                'closed' => 'dark',
                'cancelled' => 'danger'
            ];
            $status = strtolower(trim($status, "'\""));
            return $colors[$status] ?? 'secondary';
        });
    }
}




