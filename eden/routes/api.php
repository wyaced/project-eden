<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/market-test', function () {
    return [
        ["day" => "Mon", "price" => 20],
        ["day" => "Tue", "price" => 35],
        ["day" => "Wed", "price" => 28],
        ["day" => "Thu", "price" => 40],
        ["day" => "Fri", "price" => 32],
    ];
});

require __DIR__.'/api/sms.php';
require __DIR__.'/api/produce.php';
require __DIR__.'/api/market-movement.php';