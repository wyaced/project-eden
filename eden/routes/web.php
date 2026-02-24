<?php

use App\Http\Controllers\SmsController;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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

require __DIR__.'/settings.php';