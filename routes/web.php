<?php

use App\Http\Controllers\BuildingController;
use Illuminate\Support\Facades\Route;



Route::resource('/', BuildingController::class);
Route::resource('buildings', BuildingController::class);
