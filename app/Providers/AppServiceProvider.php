<?php

namespace App\Providers;

use App\Enums\RoleEnum;
use App\Logs\PatchedLaravelLog;
use App\Models\Notification;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Opcodes\LogViewer\Logs\LogType;
use Opcodes\LogViewer\LogTypeRegistrar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\App\Services\GlobalSettingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prohibit destructive database commands (migrate:fresh, migrate:reset, db:wipe)
        // in production, or when PROHIBIT_DESTRUCTIVE_COMMANDS=true,
        // or automatically when default DB is Oracle (outside automated unit tests).
        $prohibitDestructive = ! $this->app->runningUnitTests() && (
            $this->app->isProduction()
            || config('database.prohibit_destructive_commands', false)
            || config('database.default') === 'oracle'
        );

        DB::prohibitDestructiveCommands($prohibitDestructive);

        Paginator::useBootstrapFive();
        User::observe(UserObserver::class);

        View::composer('layouts.header', function ($view) {
            if (Auth::check()) {
                $headerNotifications = Notification::query()
                    ->where('user_id', Auth::id())
                    ->where('model_type', User::class)
                    ->whereNull('read_at')
                    ->latest()
                    ->limit(10)
                    ->get();
                $view->with([
                    'headerNotifications' => $headerNotifications,
                    'unreadCount'         => $headerNotifications->count(),
                ]);
            }
        });

        if (! Gate::has('viewLogViewer')) {
            Gate::define('viewLogViewer', fn ($user) => $user->hasRole([
                RoleEnum::SUPER_ADMIN->value,
                RoleEnum::ADMIN->value,
            ]));
        }

        app(LogTypeRegistrar::class)->register(LogType::LARAVEL, PatchedLaravelLog::class);
    }
}
