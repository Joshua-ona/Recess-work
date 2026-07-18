<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\PrivateComm;
use App\Models\Notification;

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
    public function boot(): void
{
    View::composer('*', function ($view) {

        if (Auth::check()) {

            $unreadMessages = PrivateComm::where('receiver_id', Auth::id())
                ->where('is_read', false)
                ->count();

            $notifCount = Notification::where('user_id', Auth::id())
                ->whereNull('read_at')
                ->count();

            $view->with([
                'unreadMessages' => $unreadMessages,
                'notifCount' => $notifCount,
            ]);
        }

    });
}
}