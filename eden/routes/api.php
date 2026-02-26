<?php

use App\Http\Controllers\Api\ProduceController;
use App\Http\Controllers\SmsController;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/show-produce-listings', [ProduceController::class, 'showListings'])->name('show.produce.listings');

Route::get('/market-test', function () {
    return [
        ["day" => "Mon", "price" => 20],
        ["day" => "Tue", "price" => 35],
        ["day" => "Wed", "price" => 28],
        ["day" => "Thu", "price" => 40],
        ["day" => "Fri", "price" => 32],
    ];
});

Route::get('/test-sms', function(TwilioService $twilio){
    // Replace with your phone number (must be verified in Twilio trial)
    $to = '+18777804236';
    $message = "Hello from Eden hackathon!";

    $twilio->sendSms($to, $message);

    return "SMS sent!";
});

Route::post('/sms/incoming', [SmsController::class, 'incoming'])->name('sms.incoming');

// Route::post('/sms/incoming', function(Request $request){
//     $from = $request->input('From');
//     $body = $request->input('Body');

//     Log::info("Received SMS from $from: $body");
//     Log::info("Replying to $from: You said $body");

//     $response = new MessagingResponse();
//     $response->message("You said: $body");

//     return response($response)->header('Content-Type', 'text/xml');
// });