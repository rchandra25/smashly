<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameSessionController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RankingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Rankings
    Route::get('/rankings', [RankingController::class, 'index'])->name('rankings.index');

    // Matches
    Route::get('/matches', [MatchController::class, 'index'])->name('matches.index');
    Route::get('/matches/create', [MatchController::class, 'create'])->name('matches.create');
    Route::post('/matches', [MatchController::class, 'store'])->name('matches.store');
    Route::get('/matches/pending', [MatchController::class, 'pending'])->name('matches.pending');
    Route::get('/matches/{game}', [MatchController::class, 'show'])->name('matches.show');
    Route::post('/matches/{game}/approve', [MatchController::class, 'approve'])->name('matches.approve');
    Route::post('/matches/{game}/reject', [MatchController::class, 'reject'])->name('matches.reject');

    // Sessions
    Route::get('/sessions', [GameSessionController::class, 'index'])->name('sessions.index');
    Route::get('/sessions/create', [GameSessionController::class, 'create'])->name('sessions.create');
    Route::post('/sessions', [GameSessionController::class, 'store'])->name('sessions.store');
    Route::get('/sessions/{session}', [GameSessionController::class, 'show'])->name('sessions.show');
    Route::post('/sessions/{session}/join', [GameSessionController::class, 'join'])->name('sessions.join');
    Route::delete('/sessions/{session}/leave', [GameSessionController::class, 'leave'])->name('sessions.leave');
    Route::patch('/sessions/{session}/status', [GameSessionController::class, 'updateStatus'])->name('sessions.updateStatus');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
