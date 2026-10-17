<?php

namespace App\Providers;

use App\Models\PortalNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.app', function ($view) {
            $count = 0;
            if (Auth::check()) {
                $count = PortalNotification::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->count();
            }
            $view->with('unreadNotificationCount', $count);
        });
    }
}
