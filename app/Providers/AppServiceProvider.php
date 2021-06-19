<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Schema;
use Gate;
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
        // Schema::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('isAdmin', function($user) {
            return $user->role === 'admin';
        });

        Gate::define('isAuthor', function ($user) {
            return $user->role === 'author';
        });

        Gate::define('isAdminOrAuthor', function($user) {
            return $user->role === 'admin' || $user->role === 'author';
        });

        Gate::define('isGuest', function ($user) {
            return $user->role === 'guest';
        });

    }
}
