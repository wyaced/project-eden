<?php

namespace App\Services;

use App\Models\ProduceListing;
use App\Models\SmsConversation;
use Illuminate\Support\Facades\Log;

class SmsListingService
{
    protected $produceService;

    protected String $from;
    protected ?String $farmerName;
    protected String $command;
    protected array $attributes;
    protected String $strAttributes;

    public function __construct(ProduceService $produceService)
    {
        $this->produceService = $produceService;
    }

    protected function parseMakeCommand(String $from, array $attributes)
    {
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
        return $listingData;
    }

    protected function parseShowCommand(array $attributes)
    {
        // Expected format for attributes: of <produce: nullable> in <location: nullable> by <name> in <location>
        // example: of tomatoes in Laguna
        // example: by Juan
        // example: in Juan

        $showRequest = [
            'produce' => null,
            'location' => null,
            'farmer_name' => null,
        ];

        if (in_array('of', $attributes)) {
            $showRequest['produce'] = $attributes[array_search('of', $attributes) + 1] ?? null;
        }

        if (in_array('in', $attributes)) {
            $showRequest['location'] = $attributes[array_search('in', $attributes) + 1] ?? null;
        }

        if (in_array('by', $attributes)) {
            $showRequest['farmer_name'] = $attributes[array_search('by', $attributes) + 1] ?? null;
        }

        return $showRequest;
    }

    protected function parseUpdateCommand(array $attributes)
    {
        // Expected format for attributes: <listing_id> UnitQuantity: <<quantity><unit>: nullable> price: <price: nullable> location: <location: nullable>
        // example: ListingId 12 UnitQuantity: 100kg price: 20php location: Laguna
        // example: ListingId 12 UnitQuantity: 9000g
        // example: ListingId 12 price: 20php location: Laguna
        // example: ListingId 12 location: Laguna

        $data = [];

        if (in_array('unitquantity:', $attributes)) {
            preg_match(
                '/^(\d+(?:\.\d+)?)([a-zA-Z]+)$/',
                $attributes[array_search('unitquantity:', $attributes) + 1] ?? '',
                $quantityUnit
            );
            $data['quantity'] = intval($quantityUnit[1] ?? null);
            $data['unit'] = $quantityUnit[2] ?? null;
        }

        if (in_array('price:', $attributes)) {
            preg_match(
                '/^(\d+(?:\.\d+)?)([a-zA-Z]+)?$/',
                $attributes[array_search('price:', $attributes) + 1] ?? '',
                $priceCurrency
            );
            $data['price_per_unit'] = floatval($priceCurrency[1] ?? null);
        }

        if (in_array('location:', $attributes)) {
            $data['location'] = $attributes[array_search('location:', $attributes) + 1] ?? null;
        }

        $updateRequest = ["id" => $attributes[0], "data" => $data];

        return $updateRequest;
    }

    protected function makeListing()
    {
        // Expected format for attributes: <produce> <quantity><unit> <price> <location> listed by <name>
        // example: tomatoes 100kg 20php Laguna listed by Juan

        $from = $this->from;
        $farmerName = $this->farmerName;
        $command = $this->command;
        $attributes = $this->attributes;
        $strAttributes = $this->strAttributes;

        Log::info("----------------------------");
        Log::info("$farmerName: $command listing $strAttributes");
        Log::info("----------------------------");

        $listingData = $this->parseMakeCommand($from, $attributes);
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
            ListingID: {$listing->id}
            Produce: {$listing->produce}
            Quantity: {$listing->quantity}{$listing->unit}
            Price: PHP{$listing->price_per_unit} / {$listing->unit}
            Location: {$listing->location}
            Farmer Name: {$listing->farmer_name}
        EOT;

        $response = ['success' => true, 'message' => $message];

        return $response;
    }

    protected function showListings()
    {
        // Expected format for attributes: of <produce: nullable> in <location: nullable> by <name> in <location>
        // example: of tomatoes in Laguna
        // example: by Juan
        // example: in Juan

        $command = $this->command;
        $attributes = $this->attributes;
        $strAttributes = $this->strAttributes;

        Log::info("----------------------------");
        Log::info("user: $command listing $strAttributes");
        Log::info("----------------------------");

        $showRequest = $this->parseShowCommand($attributes);

        $listings = $this->produceService->showListings($showRequest);
        $listingsArray = [];
        foreach ($listings as $listing) {
            $listingsArray[] = <<<EOT
            _________________________
            ID: {$listing->id}
            Produce: {$listing->produce}
            Quantity: {$listing->quantity} {$listing->unit}
            Price per unit: {$listing->price_per_unit}
            Location: {$listing->location}
            Farmer Name: {$listing->farmer_name}
            _________________________
            EOT;
        }

        $message = "Listings: \n" . implode("\n", $listingsArray) . "\nTo request purchase, specify the listing ID";

        $response = ['success' => true, 'message' => $message];

        return $response;
    }

    protected function updateListing()
    {
        // Expected format for attributes: <listing_id> UnitQuantity: <<quantity><unit>: nullable> price: <price: nullable> location: <location: nullable>
        // example: ListingId 12 UnitQuantity: 100kg price: 20php location: Laguna
        // example: ListingId 12 UnitQuantity: 100kg
        // example: ListingId 12 price: 20php location: Laguna
        // example: ListingId 12 location: Laguna

        $command = $this->command;
        $attributes = $this->attributes;
        $strAttributes = $this->strAttributes;

        Log::info("----------------------------");
        Log::info("user: $command listing $strAttributes");
        Log::info("----------------------------");

        $updateRequest = $this->parseUpdateCommand($attributes);

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

    protected function deleteListing()
    {
        // Expected format for attributes: <listing_id>
        // example: ListingId 12

        $from = $this->from;
        $command = $this->command;
        $attributes = $this->attributes;
        $strAttributes = $this->strAttributes;

        Log::info("----------------------------");
        Log::info("user: $command listing $strAttributes");
        Log::info("----------------------------");

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

    public function controlListings(String $from, String $command, array $attributes)
    {
        $this->from = $from;
        $this->farmerName = $attributes[array_search('by', $attributes) + 1] ?? null;
        $this->command = $command;
        $this->attributes = $attributes;
        $this->strAttributes = implode(' ', $attributes);

        if ($command === 'make') {
            // Expected format for attributes: <produce> <quantity><unit> <price> <location> listed by <name>
            // example: tomatoes 100kg 20php Laguna listed by Juan
            return $this->makeListing();
        }

        if ($command === 'show') {
            // Expected format for attributes: of <produce: nullable> in <location: nullable> by <name> in <location>
            // example: of tomatoes in Laguna
            // example: by Juan
            // example: in Juan
            return $this->showListings();
        }

        if ($command === 'update') {
            // Expected format for attributes: <listing_id> UnitQuantity: <<quantity><unit>: nullable> price: <price: nullable> location: <location: nullable>
            // example: ListingId 12 UnitQuantity: 100kg price: 20php location: Laguna
            // example: ListingId 12 UnitQuantity: 100kg
            // example: ListingId 12 price: 20php location: Laguna
            // example: ListingId 12 location: Laguna
            return $this->updateListing();
        }

        if ($command === 'delete') {
            // Expected format for attributes: <listing_id>
            // example: ListingId 12
            return $this->deleteListing();
        }
    }
}
