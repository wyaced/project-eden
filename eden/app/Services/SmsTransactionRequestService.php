<?php

namespace App\Services;

use App\Models\ProduceListing;
use App\Models\SmsConversation;
use App\Models\TransactionRequests;
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

    protected function parseShowCommand(array $attributes)
    {
        // Expected format for atrributes: for <for_farmer_name: nullable> from <from_farmer_name: nullable> || <transaction_request_id: nullable>
        // example: for Juan from Pedro
        // example: for Juan
        // example: from Pedro
        // example: 1

        $showRequest = [
            'for_farmer_name' => null,
            'from_farmer_name' => null,
            'transaction_request_id' => null,
        ];

        if (in_array('for', $attributes)) {
            $showRequest['for_farmer_name'] = $attributes[array_search('for', $attributes) + 1] ?? null;
        }

        if (in_array('from', $attributes)) {
            $showRequest['from_farmer_name'] = $attributes[array_search('from', $attributes) + 1] ?? null;
        }

        if (is_numeric($attributes[0] ?? null)) {
            $showRequest['transaction_request_id'] = $attributes[0];
        }

        return $showRequest;
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
                From: {$transactionRequestData['from']}
                To: {$listing->farmer_name}
                Listing:
                    Produce: {$listing->produce}
                    Quantity: {$listing->quantity}{$listing->unit}
                    Price: PHP{$listing->price_per_unit} / {$listing->unit}
                    Farmer: {$listing->farmer_name}
                    Location: {$listing->location}
                Requested Quantity: {$transactionRequestData['unit_quantity']}{$listing->unit}
            Send This Transaction Request to {$listing->farmer_name}?
            Reply YES or NO.
            EOT;

        $response = ['success' => 'pending', 'message' => $message];

        return $response;
    }

    protected function showTransactionRequests()
    {
        // Expected format for atrributes: for <for_farmer_name: nullable> from <from_farmer_name: nullable> || <transaction_request_id>
        // example: for Juan from Pedro
        // example: for Juan
        // example: from Pedro
        // example: 1

        $command = $this->command;
        $attributes = $this->attributes;
        $strAttributes = $this->strAttributes;

        Log::info("----------------------------");
        Log::info("user: $command TransactionRequest $strAttributes");
        Log::info("----------------------------");

        $showRequest = $this->parseShowCommand($attributes);

        $transactionRequests = $this->transactionRequestService->showTransactionRequests($showRequest);
        $transactionRequestsArray = [];
        foreach ($transactionRequests as $transactionRequest) {
            $listing = ProduceListing::find($transactionRequest->listing_id);
            $transactionRequestsArray[] = <<<EOT
            _________________________
            ID: {$transactionRequest->id}
            From: {$transactionRequest->from}
            To: {$transactionRequest->to}
            Listing:
                Produce: {$listing->produce}
                Quantity: {$listing->quantity}{$listing->unit}
                Price: PHP{$listing->price_per_unit} / {$listing->unit}
                Location: {$listing->location}
            Unit Quantity: {$transactionRequest->unit_quantity}{$transactionRequest->unit}
            Status: {$transactionRequest->status}
            _________________________
            EOT;
        }

        $message = "Transaction Requests: \n" . implode("\n", $transactionRequestsArray);

        $response = ['success' => true, 'message' => $message];

        return $response;
    }

    protected function cancelTransactionRequest()
    {
        // Expected format for attributes: <transaction_request_id>
        // example: 1

        $from = $this->from;
        $command = $this->command;
        $attributes = $this->attributes;
        $strAttributes = $this->strAttributes;

        Log::info("----------------------------");
        Log::info("user: $command TransactionRequest $strAttributes");
        Log::info("----------------------------");

        $transactionRequest = TransactionRequests::find(intval($attributes[0] ?? 0));

        if (is_null($transactionRequest)) {
            return [
                'success' => false,
                'message' => "Invalid request/Transaction Request ID. Please try again.",
            ];
        }

        SmsConversation::create([
            'farmer_phone' => $from,
            'action' => 'cancel_transaction_request',
            'status' => 'pending',
            'data' => ['transaction_request_id' => $transactionRequest->id],
        ]);

        $produceListing = ProduceListing::find($transactionRequest->listing_id);

        if (is_null($produceListing)) {
            return [
                'success' => false,
                'message' => "Associated listing with ID {$transactionRequest->listing_id} not found.",
            ];
        }

        $message = <<<EOT
        Canceling Transaction Request ID {$transactionRequest->id}...
        From: {$transactionRequest->from}
        To: {$transactionRequest->to}
        Listing:
            Produce: {$produceListing->produce}
            Quantity: {$produceListing->quantity}{$produceListing->unit}
            Price: PHP{$produceListing->price_per_unit} / {$produceListing->unit}
            Farmer: {$produceListing->farmer_name}
            Location: {$produceListing->location}
        Requested Quantity: {$transactionRequest->unit_quantity}{$produceListing->unit}
        Are you sure you want to cancel this Transaction Request?
        Reply YES or NO.
        EOT;

        $response = ['success' => 'pending', 'message' => $message];

        return $response;
    }

    protected function acceptTransactionRequest()
    {
        // Expected format for attributes: <transaction_request_id>
        // example: 1

        $from = $this->from;
        $command = $this->command;
        $attributes = $this->attributes;
        $strAttributes = $this->strAttributes;

        Log::info("----------------------------");
        Log::info("user: $command TransactionRequest $strAttributes");
        Log::info("----------------------------");

        $transactionRequestId = intval($attributes[0] ?? 0);
        $transactionRequest = TransactionRequests::find($transactionRequestId);

        if (is_null($transactionRequest)) {
            return [
                'success' => false,
                'message' => "Invalid request/Transaction Request ID. Please try again.",
            ];
        }

        $listing = ProduceListing::find($transactionRequest->listing_id);

        if (is_null($listing)) {
            return [
                'success' => false,
                'message' => "Associated listing with ID {$transactionRequest->listing_id} not found.",
            ];
        }

        SmsConversation::create([
            'farmer_phone' => $from,
            'action' => 'accept_transaction_request',
            'status' => 'pending',
            'data' => ['transaction_request_id' => $transactionRequestId],
        ]);

        $message = <<<EOT
        Accepting TransactionRequestID {$transactionRequest->id}...
            From: {$transactionRequest->from}
            To: {$listing->farmer_name}
            Listing:
                Produce: {$listing->produce}
                Quantity: {$listing->quantity}{$listing->unit}
                Price: PHP{$listing->price_per_unit} / {$listing->unit}
                Farmer: {$listing->farmer_name}
                Location: {$listing->location}
            Requested Quantity: {$transactionRequest->unit_quantity}{$listing->unit}
        Are you sure you want to accept this Transaction Request?
        Reply YES or NO.
        EOT;

        $response = ['success' => 'pending', 'message' => $message];

        return $response;
    }

    protected function rejectTransactionRequest()
    {
        // Expected format for attributes: <transaction_request_id>
        // example: 1

        $from = $this->from;
        $command = $this->command;
        $attributes = $this->attributes;
        $strAttributes = $this->strAttributes;

        Log::info("----------------------------");
        Log::info("user: $command TransactionRequest $strAttributes");
        Log::info("----------------------------");

        $transactionRequestId = intval($attributes[0] ?? 0);
        $transactionRequest = TransactionRequests::find($transactionRequestId);

        if (is_null($transactionRequest)) {
            return [
                'success' => false,
                'message' => "Invalid request/Transaction Request ID. Please try again.",
            ];
        }

        $listing = ProduceListing::find($transactionRequest->listing_id);

        if (is_null($listing)) {
            return [
                'success' => false,
                'message' => "Associated listing with ID {$transactionRequest->listing_id} not found.",
            ];
        }

        SmsConversation::create([
            'farmer_phone' => $from,
            'action' => 'reject_transaction_request',
            'status' => 'pending',
            'data' => ['transaction_request_id' => $transactionRequestId],
        ]);

        $message = <<<EOT
        Rejecting TransactionRequestID {$transactionRequest->id}...
            From: {$transactionRequest->from}
            To: {$listing->farmer_name}
            Listing:
                Produce: {$listing->produce}
                Quantity: {$listing->quantity}{$listing->unit}
                Price: PHP{$listing->price_per_unit} / {$listing->unit}
                Farmer: {$listing->farmer_name}
                Location: {$listing->location}
            Requested Quantity: {$transactionRequest->unit_quantity}{$listing->unit}
        Are you sure you want to reject this Transaction Request?
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

        if ($command === 'show') {
            // Expected format for atrributes: for <to_farmer_name: nullable> from <from_farmer_name: nullable> || <transaction_request_id>
            // example: for Juan from Pedro
            // example: for Juan
            // example: from Pedro
            // example: 1
            return $this->showTransactionRequests();
        }

        if ($command === 'cancel') {
            // Expected format for attributes: <transaction_request_id>
            // example: 1
            return $this->cancelTransactionRequest();
        }

        if ($command === 'accept') {
            // Expected format for attributes: <transaction_request_id>
            // example: 1
            return $this->acceptTransactionRequest();
        }

        if ($command === 'reject') {
            // Expected format for attributes: <transaction_request_id>
            // example: 1
            return $this->rejectTransactionRequest();
        }
    }
}
