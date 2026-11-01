<?php

use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GlobalSettingController;
use App\Http\Controllers\HealthMonitoringController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebPushController;
use Illuminate\Support\Facades\Route;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware(['auth', 'force.change.password', 'check.password.expiry', 'request.logger'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/update-web-fcm', [WebPushController::class, 'update'])->name('update-web-fcm');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::post('list', [NotificationController::class, 'notificationList'])->name('list');
        Route::post('mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
        Route::post('{notification}/mark-read', [NotificationController::class, 'markRead'])->name('mark-read');
    });

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('counts', [DashboardController::class, 'counts'])->name('counts');
    });

    // Users Management
    Route::resource('users', UserController::class)->except(['create']);
    Route::post('users/list', [UserController::class, 'userList'])->name('users.list');
    Route::post('users/{user}/update-password', [UserController::class, 'updatePassword'])->name('users.update-password');

    // Roles & Permissions Management
    Route::resource('roles', RoleController::class);
    Route::post('roles/list', [RoleController::class, 'roleList'])->name('roles.list');

    // Global Settings
    Route::get('global-settings', [GlobalSettingController::class, 'index'])->name('global-setting.index');
    Route::put('global-settings', [GlobalSettingController::class, 'update'])->name('global-setting.update');

    // Infrastructure Monitoring
    Route::prefix('health-monitoring')->name('health-monitoring.')->group(function () {
        Route::get('/', [HealthMonitoringController::class, 'index'])->name('index');
        Route::get('chart-data/{component}', [HealthMonitoringController::class, 'chartData'])->name('chart-data');
        Route::get('api-traffic/by-app', [HealthMonitoringController::class, 'apiTrafficByApp'])->name('api-traffic.by-app');
        Route::get('api-traffic/by-route', [HealthMonitoringController::class, 'apiTrafficByRoute'])->name('api-traffic.by-route');
        Route::get('outbound-connectivity', [HealthMonitoringController::class, 'outboundConnectivity'])->name('outbound-connectivity');
    });

    // Password & Profile
    Route::get('password/change', [PasswordController::class, 'index'])->name('password.index');
    Route::put('password/update', [PasswordController::class, 'update'])->name('password.change');

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('profile.index');
        Route::get('edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('update', [ProfileController::class, 'update'])->name('profile.update');
    });
});

Route::get('logs', [LogViewerController::class, 'index'])->middleware(['auth', 'force.change.password', 'check.password.expiry', 'can:viewLogViewer']);

require __DIR__ . '/auth.php';
