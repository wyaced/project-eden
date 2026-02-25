<?php

use App\Http\Controllers\Api\ProduceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/show-produce-listings', [ProduceController::class, 'showListings'])->name('show.produce.listings');
