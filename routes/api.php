<?php

use App\Http\Controllers\Api\MetricsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/helpdesk/metrics', [MetricsController::class, 'index'])
        ->middleware(['auth:sanctum', 'throttle:60,1'])
        ->name('helpdesk.metrics');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
