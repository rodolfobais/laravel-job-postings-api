<?php

use App\Infrastructure\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;

Route::get('/jobs', [JobController::class, 'index']);
Route::post('/jobs', [JobController::class, 'store']);
Route::post('/subscriptions', [JobController::class, 'subscribe']);
