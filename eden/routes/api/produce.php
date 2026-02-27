<?php

use App\Http\Controllers\Api\ProduceController;
use Illuminate\Support\Facades\Route;

Route::get('/produce-listings', [ProduceController::class, 'showListings'])->name('show.produce.listings');