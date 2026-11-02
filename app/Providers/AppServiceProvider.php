<?php

namespace App\Providers;

use App\Models\Notifikasi;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale('id');
        Paginator::defaultView('pagination.matcha');

        // Badge jumlah notifikasi belum dibaca di layout utama
        View::composer('layouts.app', function ($view) {
            $user = Auth::user();

            $view->with('notifBelumDibaca', $user
                ? Notifikasi::where('user_id', $user->id)->whereNull('dibaca_at')->count()
                : 0);
        });
    }
}
