<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LinkController;
use App\Http\Middleware\StopBotMiddleware;
use Illuminate\Support\Facades\Route;

// Root redirect for better UX
Route::get('/', function () {
    return session('authed') ? redirect()->route('dashboard') : redirect()->route('login');
});

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard & create
Route::middleware(function ($request, $next) {
    if (!$request->session()->get('authed')) { return redirect()->route('login'); }
    return $next($request);
})->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/create', [LinkController::class, 'showCreate'])->name('links.create');
    Route::post('/create', [LinkController::class, 'store'])->name('links.store');
});

// Redirect with StopBot middleware
Route::middleware([StopBotMiddleware::class])->get('/{code}', [LinkController::class, 'redirect'])->name('links.redirect');
