<?php

namespace App\Providers;

use Backpack\PermissionManager\app\Http\Controllers\RoleCrudController;
use Backpack\PermissionManager\app\Http\Controllers\UserCrudController;
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
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(
            RoleCrudController::class, //this is package controller
            \App\Http\Controllers\Admin\PermissionManager\RoleCrudController::class //this should be your own controller
        );
        $this->app->bind(
            UserCrudController::class, //this is package controller
            \App\Http\Controllers\Admin\PermissionManager\UserCrudController::class //this should be your own controller
        );
    }
}
