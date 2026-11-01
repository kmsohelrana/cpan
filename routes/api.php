<?php

use App\Http\Controllers\Api\V1\HealthCheckController;
use Illuminate\Support\Facades\Route;

Route::middleware(['request.logger', 'locale'])->prefix('v1')->group(function () {
    Route::get('/heartbeat', [HealthCheckController::class, 'heartbeat']);
});
