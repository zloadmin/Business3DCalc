<?php

use App\Http\Controllers\BuildingController;
use App\Http\Controllers\GameSessionController;
use Illuminate\Support\Facades\Route;



Route::resource('/', BuildingController::class);
Route::get('buildings/{building}/updates', [BuildingController::class, 'updates'])
    ->name('buildings.updates');

Route::resource('buildings', BuildingController::class);
Route::prefix('game-session')->group(function () {
    Route::get('/', [GameSessionController::class, 'create'])->name('game-session.create');
    Route::post('/', [GameSessionController::class, 'store']);
    Route::get('/{gameSession}', [GameSessionController::class, 'show'])->name('game-session.show');
    Route::post('/{gameSession}/buy-building', [GameSessionController::class, 'buyBuilding'])->name('game-session.buy-building');
    Route::post('/{gameSession}/upgrade-game-building/{gameBuilding}', [GameSessionController::class, 'upgrade'])->name('game-session.upgrade-game-building');
});

