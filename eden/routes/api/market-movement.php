<?php

use App\Http\Controllers\MarketMovementController;
use App\Models\MarketMovement;
use Illuminate\Support\Facades\Route;

Route::controller(MarketMovementController::class)->group(function () {
    Route::get('/market-movement-test', function () {
        return response()->json(MarketMovement::all());
    });
    Route::get('/market-movement', 'getMarketMovement')->name('get.market.movement');
    Route::get('/market-movement-records/{type}/{produce}/{location?}', 'getMarketMovementRecords')->name('index.market.movement');
    Route::get('/record-market-movement', 'recordMarketMovement')->name('record.market.movement');
});
