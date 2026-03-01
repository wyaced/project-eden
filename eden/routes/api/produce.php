<?php

use App\Http\Controllers\Api\ProduceController;
use Illuminate\Support\Facades\Route;

Route::controller(ProduceController::class)->group(function () {
    Route::get('/produce-names', 'getProduceNames')->name('get.produce.names');
    Route::get('/produce-listings/{produce?}', 'showListings')->name('show.produce.listings');
});
