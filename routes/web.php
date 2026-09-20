<?php

use App\Http\Controllers\BuildingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('buildings', BuildingController::class);
