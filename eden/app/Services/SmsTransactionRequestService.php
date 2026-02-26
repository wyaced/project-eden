<?php

namespace App\Services;

use App\Models\ProduceListing;
use App\Models\SmsConversation;
use App\Services\TransactionRequestService;
use Illuminate\Support\Facades\Log;

class SmsTransactionRequestService
{
    protected $transactionRequestService;

    protected String $from;
    protected ?String $farmerName;
    protected String $command;
    protected array $attributes;
    protected String $strAttributes;

    public function __construct(TransactionRequestService $transactionRequestService)
    {
        $this->transactionRequestService = $transactionRequestService;
    }

    protected function parseMakeCommand(String $from, array $attributes)
    {
        // Expected format for attributes: ListingId <listing_id> <UnitQuantity> requested by <name>
        // example: ListingId 1 50kg requested by Juan

        preg_match('/^(\d+(?:\.\d+)?)([a-zA-Z]+)$/', $attributes[2] ?? '', $quantityUnit);

        $transactionRequestData = [
            'from' => $attributes[array_search('by', $attributes) + 1] ?? null,
            'from_phone' => $from,
            'listing_id' => $attributes[1] ?? null,
            'unit_quantity' => $quantityUnit[1] ?? null,
        ];

        return $transactionRequestData;
    }

    protected function makeTransactionRequest()
    {
        // Expected format for attributes: ListingId <listing_id> <UnitQuantity> requested by <name>
        // example: ListingId 1 50kg requested by Juan

        $from = $this->from;
        $farmerName = $this->farmerName;
        $command = $this->command;
        $attributes = $this->attributes;
        $strAttributes = $this->strAttributes;

        Log::info("----------------------------");
        Log::info("$farmerName: $command TransactionRequest $strAttributes");
        Log::info("----------------------------");

        $transactionRequestData = $this->parseMakeCommand($from, $attributes);
        $listing = ProduceListing::find($transactionRequestData['listing_id']);

        if (is_null($listing)) {
            return [
                'success' => false,
                'message' => "Listing with ID {$transactionRequestData['listing_id']} not found.",
            ];
        }

        SmsConversation::create([
            'farmer_phone' => $from,
            'action' => 'make_transaction_request',
            'status' => 'pending',
            'data' => ['transaction_request_data' => $transactionRequestData],
        ]);

        $message = <<<EOT
            Creating TransactionRequest...
                From: {$transactionRequestData['from']} ({$transactionRequestData['from_phone']})
                To: {$listing->farmer_name} ({$listing->farmer_phone})
                Listing:
                    Produce: {$listing->produce}
                    Quantity: {$listing->quantity}{$listing->unit}
                    Price: PHP{$listing->price_per_unit} / {$listing->unit}
                    Farmer: {$listing->farmer_name}
                    Location: {$listing->location}
                Requested Quantity: {$transactionRequestData['unit_quantity']}{$listing->unit}
            Send This Transaction Request to {$listing->farmer_name} ({$listing->farmer_phone})?
            Reply YES or NO.
            EOT;

        $response = ['success' => 'pending', 'message' => $message];

        return $response;
    }

    public function controlTransactionRequests(String $from, String $command, array $attributes)
    {
        $this->from = $from;
        $this->farmerName = $attributes[array_search('by', $attributes) + 1] ?? null;
        $this->command = $command;
        $this->attributes = $attributes;
        $this->strAttributes = implode(' ', $attributes);

        if ($command === 'make') {
            // Expected format for attributes: ListingId <listing_id> <UnitQuantity> requested by <name>
            // example: ListingId 1 50kg requested by Juan
            return $this->makeTransactionRequest();
        }
    }
}
