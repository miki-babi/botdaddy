<?php

use App\Http\Controllers\BotController;
use App\Http\Controllers\BotDashboardController;
use App\Http\Controllers\TelegramWebhookController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::post('/telegram/webhook', TelegramWebhookController::class)->name('telegram.webhook');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [BotDashboardController::class, 'index'])->name('dashboard');

    Route::post('bots', [BotController::class, 'store'])->name('bots.store');
    Route::patch('bots/{bot}', [BotController::class, 'update'])->name('bots.update');
    Route::post('bots/{bot}/publish', [BotController::class, 'publish'])->name('bots.publish');
    Route::post('bots/{bot}/broadcast', [BotController::class, 'broadcast'])->name('bots.broadcast');
    Route::delete('bots/{bot}', [BotController::class, 'destroy'])->name('bots.destroy');
});

require __DIR__.'/settings.php';
