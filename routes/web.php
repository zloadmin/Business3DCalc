<?php

use App\Http\Controllers\BuildingController;
use Illuminate\Support\Facades\Route;



Route::resource('/', BuildingController::class);
Route::get('buildings/{building}/updates', [BuildingController::class, 'updates'])
    ->name('buildings.updates');

Route::resource('buildings', BuildingController::class);
