<?php

use App\Infrastructure\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

Route::get('/jobs', [JobController::class, 'index']);

// Stricter throttle than the default 60/min API-wide limit: these two write
// endpoints can trigger an email send (job creation notifies subscribers,
// subscribing registers a new recipient) with no auth in front of them.
Route::post('/jobs', [JobController::class, 'store'])->middleware('throttle:10,1');
Route::post('/subscriptions', [JobController::class, 'subscribe'])->middleware('throttle:10,1');
