<?php

namespace App\Http\Controllers;

use App\Services\TwilioService;
use App\Services\ProduceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Twilio\TwiML\MessagingResponse;

class SmsController extends Controller
{
    protected $twilio;
    protected $produceService;

    public function __construct(TwilioService $twilio, ProduceService $produceService)
    {
        $this->twilio = $twilio;
        $this->produceService = $produceService;
    }

    protected function controlListings($from, $command, $attributes)
    {
        if ($command === 'make') {
            // Expected format for attributes: <produce> <quantity><unit> <price> <location> listed by <name>
            // example: tomatoes 100kg 20php Laguna listed by Juan

            // Log::info("Parsing listing attributes: " . json_encode($attributes));
            preg_match('/^(\d+(?:\.\d+)?)([a-zA-Z]+)$/', $attributes[1] ?? '', $quantityUnit);
            preg_match('/^(\d+(?:\.\d+)?)([a-zA-Z]+)$/', $attributes[2] ?? '', $priceCurrency);

            $listingData = [
                'farmer_phone' => $from,
                'produce' => $attributes[0] ?? null,
                'quantity' => intval($quantityUnit[1] ?? 0),
                'unit' => $quantityUnit[2] ?? null,
                'price_per_unit' => floatval($priceCurrency[1] ?? 0),
                'location' => $attributes[3] ?? null,
                'farmer_name' => end($attributes) ?? null,
            ];

            // Log::info("Parsed listing data: " . json_encode($quantityUnit));
            Log::info("Creating listing: " . json_encode($listingData));
            $response = $this->produceService->createListing($listingData);

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
            $response = $this->controlListings($from, $tokens['command'], $tokens['attributes']);
            if (!$response['success']) {
                Log::error("Eden: Failed to create listing for farmer $from");
                Log::error("=== SMS Conversation End ===");
                return;
            }
            $listing = $response['listing'];
            $message = <<<EOT
            Listing created successfully!
                Produce: {$listing->produce}
                Quantity: {$listing->quantity} {$listing->unit}
                Price per unit: {$listing->price_per_unit}
                Location: {$listing->location}
                Farmer Name: {$listing->farmer_name}
                Farmer Phone: {$listing->farmer_phone}
            EOT;
            $response = new MessagingResponse();
            $response->message($message);
            Log::info("Eden: $message");
            Log::info("=== SMS Conversation End ===");
            return response($response)->header('Content-Type', 'text/xml');
        }
    }
}
