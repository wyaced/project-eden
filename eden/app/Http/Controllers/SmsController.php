<?php

namespace App\Http\Controllers;

use App\Services\ProduceService;
use App\Services\SmsListingService;
use App\Services\TwilioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\MessagingResponse;

class SmsController extends Controller
{
    protected $twilio;
    protected $produceService;
    protected $smsListingService;

    public function __construct(TwilioService $twilio, ProduceService $produceService, SmsListingService $smsListingService)
    {
        $this->twilio = $twilio;
        $this->produceService = $produceService;
        $this->smsListingService = $smsListingService;
    }

    protected function controlListings($from, $command, $attributes)
    {
        if ($command === 'make') {
            // Expected format for attributes: <produce> <quantity><unit> <price> <location> listed by <name>
            // example: tomatoes 100kg 20php Laguna listed by Juan

            $listingData = $this->smsListingService->parseMakeCommand($from, $attributes);
            $makeListingResponse = $this->produceService->createListing($listingData);

            if (!$makeListingResponse['success']) {
                Log::error("Eden: Failed to create listing for farmer $from");
                Log::error("=== SMS Conversation End ===");

                $response = ['success' => false, 'message' => "Failed to create listing. Please check your command format and try again."];

                return $response;
            }
            $listing = $makeListingResponse['listing'];
            $message = <<<EOT
            Listing created successfully!
                Produce: {$listing->produce}
                Quantity: {$listing->quantity} {$listing->unit}
                Price per unit: {$listing->price_per_unit}
                Location: {$listing->location}
                Farmer Name: {$listing->farmer_name}
                Farmer Phone: {$listing->farmer_phone}
            EOT;

            $response = ['success' => true, 'message' => $message];

            return $response;
        }

        if ($command === 'show') {
            // Expected format for attributes: <produce> <location: nullable> by <name: nullable>
            // example: tomatoes Laguna by Juan

            $showRequest = $this->smsListingService->parseShowCommand($attributes);
            $listings = $this->produceService->showListings($showRequest);
            $listingsArray = [];
            foreach ($listings as $listing) {
                $listingsArray[] = 
                    <<<EOT
                    ==================
                    ID: {$listing->id}
                    Produce: {$listing->produce}
                    Quantity: {$listing->quantity} {$listing->unit}
                    Price per unit: {$listing->price_per_unit}
                    Location: {$listing->location}
                    Farmer Name: {$listing->farmer_name}
                    Farmer Phone: {$listing->farmer_phone}
                    ==================
                    EOT;
            }
            $message = "Listings: \n" . implode("\n", $listingsArray) . "\nTo request purchase, specify the listing ID";

            $response = ['success' => true, 'message' => $message];

            return $response;
        }
    }

    public function incoming(Request $request)
    {

        $from = $request->input('From');

        $body = strtolower($request->input('Body'));

        Log::info("=== SMS Conversation Start ===");

        // Expected format: command listing attributes
        // example: make listing tomatoes 100kg 20php Laguna listed by Juan
        $words = explode(' ', $body);
        $tokens = [
            'command' => $words[0] ?? null,
            'attributes' => array_slice($words, 2) ?? null,
        ];
        $senderName = end($tokens['attributes']);

        Log::info("$from ($senderName): $body");

        if (str_contains($body, 'listing')) {
            $listingResponse = $this->controlListings($from, $tokens['command'], $tokens['attributes']);

            $message = $listingResponse['message'] ?? "Sorry, we couldn't process your request. Please check your command format and try again.";

            $response = new MessagingResponse();
            $response->message($message);
            foreach (explode("\n", $message) as $line) {
                Log::info("Eden: $line");
            }
            Log::info("=== SMS Conversation End ===");
            return response($response)->header('Content-Type', 'text/xml');
        }
    }
}
