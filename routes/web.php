<?php

use App\Http\Controllers\LeaderboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/leaderboard', [LeaderboardController::class, 'index'])->middleware('auth');

Route::post('/score', 'App\Http\Controllers\ScoreController@store')->middleware('auth');


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
