<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/food-spots', [App\Http\Controllers\FoodSpotController::class, 'index'])->name('food-spots.index');
    Route::post('/food-spots', [App\Http\Controllers\FoodSpotController::class, 'store'])->name('food-spots.store');
});


