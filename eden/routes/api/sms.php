<?php

use App\Http\Controllers\SmsController;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Route;

Route::get('/test-sms', function(TwilioService $twilio){
    // Replace with your phone number (must be verified in Twilio trial)
    // twilio 1
    // $to = '+18777804236';
    // twilio 2
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