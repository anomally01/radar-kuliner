<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/api/food-spots', [App\Http\Controllers\FoodSpotController::class, 'index']);
    Route::post('/api/food-spots', [App\Http\Controllers\FoodSpotController::class, 'store']);
});

