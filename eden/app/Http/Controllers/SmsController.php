<?php

namespace App\Http\Controllers;

use App\Models\ProduceListing;
use App\Models\SmsConversation;
use App\Services\ProduceService;
use App\Services\SmsConversationService;
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
    protected $smsConversationService;

    public function __construct(
        TwilioService $twilio,
        ProduceService $produceService,
        SmsListingService $smsListingService,
        SmsConversationService $smsConversationService,
    ) {
        $this->twilio = $twilio;
        $this->produceService = $produceService;
        $this->smsListingService = $smsListingService;
        $this->smsConversationService = $smsConversationService;
    }

    protected function controlListings($from, $command, $attributes)
    {
        $farmerName = end($attributes) ?? null;
        $strAttributes = implode(' ', $attributes);
        if ($command === 'make') {
            // Expected format for attributes: <produce> <quantity><unit> <price> <location> listed by <name>
            // example: tomatoes 100kg 20php Laguna listed by Juan

            Log::info("$farmerName: $command listing $strAttributes");

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
            EOT;

            $response = ['success' => true, 'message' => $message];

            return $response;
        }

        if ($command === 'show') {
            // Expected format for attributes: of <produce: nullable> in <location: nullable> by <name> in <location>
            // example: of tomatoes in Laguna
            // example: by Juan
            // example: in Juan

            Log::info("user: $command listing $strAttributes");

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
                    ==================
                    EOT;
            }
            $message = "Listings: \n" . implode("\n", $listingsArray) . "\nTo request purchase, specify the listing ID";

            $response = ['success' => true, 'message' => $message];

            return $response;
        }

        if ($command === 'update') {
            // Expected format for attributes: <listing_id> UnitQuantity: <<quantity><unit>: nullable> price: <price: nullable> location: <location: nullable>
            // example: ListingId 12 UnitQuantity: 100kg price: 20php location: Laguna
            // example: ListingId 12 UnitQuantity: 100kg
            // example: ListingId 12 price: 20php location: Laguna
            // example: ListingId 12 location: Laguna

            Log::info("user: $command listing $strAttributes");

            $updateRequest = $this->smsListingService->parseUpdateCommand($attributes);

            $produceListing = ProduceListing::find($updateRequest['id']);
            if (!$produceListing) {
                Log::error("Eden: Failed to update ListingID $updateRequest[id] - Listing not found");
                Log::error("=== SMS Conversation End ===");

                $response = ['success' => false, 'message' => "Failed to update listing. Listing ID $updateRequest[id] not found."];

                return $response;
            }

            $updateListingResponse = $this->produceService->updateListing($produceListing, $updateRequest['data']);

            if (!$updateListingResponse['success']) {
                Log::error("Eden: Failed to update ListingID $updateRequest[id]");
                Log::error("=== SMS Conversation End ===");

                $response = ['success' => false, 'message' => "Failed to update listing. Please check your command format and try again."];

                return $response;
            }
            $listing = $updateListingResponse['listing'];
            $message = <<<EOT
            ListingID {$listing->id} updated successfully!
                Produce: {$listing->produce}
                Quantity: {$listing->quantity} {$listing->unit}
                Price per unit: {$listing->price_per_unit}
                Location: {$listing->location}
                Farmer Name: {$listing->farmer_name}
            EOT;

            $response = ['success' => true, 'message' => $message];

            return $response;
        }

        if ($command === 'delete') {
            // Expected format for attributes: <listing_id>
            // example: ListingId 12

            Log::info("user: $command listing $strAttributes");

            $listingId = $attributes[0] ?? null;

            if (!$listingId) {
                Log::error("Eden: Failed to delete ListingID $listingId - Missing listing ID");
                Log::error("=== SMS Conversation End ===");

                $response = ['success' => false, 'message' => "Failed to delete listing. Please specify the listing ID."];

                return $response;
            }

            $produceListing = ProduceListing::find($listingId);

            if (!$produceListing) {
                Log::error("Eden: Failed to delete listing for farmer $from - Listing ID $listingId not found");
                Log::error("=== SMS Conversation End ===");

                $response = ['success' => false, 'message' => "Failed to delete listing. Listing ID $listingId not found."];

                return $response;
            }

            // Create a pending conversation for delete confirmation
            SmsConversation::create([
                'farmer_phone' => $from,
                'action' => 'delete_listing',
                'status' => 'pending',
                'data' => ['listing_id' => $produceListing->id],
            ]);

            $message = <<<EOT
            Deleting ListingID {$listingId}...
                Produce: {$produceListing->produce}
                Quantity: {$produceListing->quantity} {$produceListing->unit}
                Price per unit: {$produceListing->price_per_unit}
                Location: {$produceListing->location}
                Farmer Name: {$produceListing->farmer_name}
            Are you sure you want to delete this listing?
            Reply YES or NO
            EOT;

            // $deleteListingResponse = $this->produceService->deleteListing($produceListing);

            // if (!$deleteListingResponse['success']) {
            //     Log::error("Eden: Failed to delete listing ID $listingId");
            //     Log::error("=== SMS Conversation End ===");

            //     $response = ['success' => false, 'message' => "Failed to delete listing. Please try again later."];

            //     return $response;
            // }

            // $message = $message . "\nListingID {$listingId} deleted successfully!";

            $response = ['success' => 'pending', 'message' => $message];

            return $response;
        }
    }

    public function incoming(Request $request)
    {
        $from = $request->input('From');

        $body = strtolower($request->input('Body'));

        $conversation = SmsConversation::where('farmer_phone', $from)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (is_null($conversation)) {
            Log::info("=== SMS Conversation Start ===");
        } else {
            $action = $conversation->action;
            $data = $conversation->data;
            $userResponse = $body;

            Log::info("user: $userResponse");

            if ($action === 'delete_listing') {
                $conversationResponse = $this->smsConversationService->deleteListingConversation($userResponse, $data);
            }

            if ($conversationResponse['success']) {
                $conversation->update(['status' => 'completed']);
            } elseif ($conversationResponse['success'] === false) {
                $conversationResponse['success'] = 'pending';
            }
            $message = $conversationResponse['message'] ?? "Sorry, we couldn't process your response.";

            $response = [
                'success' => $conversationResponse['success'],
                'message' => $message,
            ];
        }

        // Expected format: command listing attributes
        // example: make listing tomatoes 100kg 20php Laguna listed by Juan
        $words = explode(' ', $body);
        $tokens = [
            'command' => $words[0] ?? null,
            'attributes' => array_slice($words, 2) ?? null,
        ];


        if (
            str_contains($body, 'listing') ||
            str_contains($body, 'listings') ||
            str_contains($body, 'listingid')
        ) {
            $listingResponse = $this->controlListings($from, $tokens['command'], $tokens['attributes']);
            if (!$listingResponse['success'] || $listingResponse['success'] != 'pending') {
                Log::error("Eden: Failed to process command for farmer $from");
                Log::error("=== SMS Conversation End ===");
            }

            $message = $listingResponse['message'] ?? "Sorry, we couldn't process your request. Please check your command format and try again.";

            $response = [
                'success' => $listingResponse['success'],
                'message' => $message,
            ];
        }

        $reply = new MessagingResponse();
        $reply->message($message);
        foreach (explode("\n", $message) as $line) {
            Log::info("Eden: $line");
        }
        if (is_bool($response['success'])) {
            Log::info("=== SMS Conversation End ===");
        }
        return response($reply)->header('Content-Type', 'text/xml');
    }
}
