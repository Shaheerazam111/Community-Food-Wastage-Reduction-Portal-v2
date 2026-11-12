<?php

namespace App\Providers;

use App\Models\PortalNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view) {
            $unread = 0;
            if (Auth::check()) {
                $unread = PortalNotification::where('user_id', Auth::id())->whereNull('read_at')->count();
            }
            $view->with('unreadNotifications', $unread);
        });
    }
}
