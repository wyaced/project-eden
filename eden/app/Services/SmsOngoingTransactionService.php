<?php

namespace App\Services;

use App\Models\OngoingTransactions;
use App\Models\ProduceListing;
use App\Models\SmsConversation;
use Illuminate\Support\Facades\Log;

class SmsOngoingTransactionService
{
    protected $ongoingTransactionService;

    protected String $from;
    protected ?String $farmerName;
    protected String $command;
    protected array $attributes;
    protected String $strAttributes;

    public function __construct(OngoingTransactionService $ongoingTransactionService)
    {
        $this->ongoingTransactionService = $ongoingTransactionService;
    }

    protected function parseShowCommand(array $attributes)
    {
        // Expected format for attributes: of <of_farmer_name> with <with_farmer_name:nullable> OngoingTransactionId: <ongoing_transaction_id:nullable>
        // example: of Pedro with Juan OngoingTransactionId: 1
        // example: of Pedro OngoingTransactionId: 1
        // example: of Pedro with Juan

        if (!in_array('of', $attributes)) {
            return null;
        }

        $showRequest = [
            'of_farmer_name' => $attributes[array_search('of', $attributes) + 1] ?? null,
            'with_farmer_name' => null,
            'ongoing_transaction_id' => null,
        ];

        if (in_array('with', $attributes)) {
            $showRequest['with_farmer_name'] = $attributes[array_search('with', $attributes) + 1] ?? null;
        }

        if (in_array('ongoingtransactionid', $attributes)) {
            $showRequest['ongoing_transaction_id'] = $attributes[array_search('ongoingtransactionid', $attributes) + 1] ?? null;
        }

        return $showRequest;
    }

    protected function showOngoingTransactions()
    {
        // Expected format for attributes: of <of_farmer_name> with <with_farmer_name:nullable> OngoingTransactionId: <ongoing_transaction_id:nullable>
        // example: of Pedro with Juan OngoingTransactionId: 1
        // example: of Pedro OngoingTransactionId: 1
        // example: of Pedro with Juan

        $command = $this->command;
        $attributes = $this->attributes;
        $strAttributes = $this->strAttributes;

        Log::info("----------------------------");
        Log::info("user: $command TransactionRequest $strAttributes");
        Log::info("----------------------------");

        $showRequest = $this->parseShowCommand($attributes);

        $ongoingTransactions = $this->ongoingTransactionService->showOngoingTransactions($showRequest);
        $ongoingTransactionsArray = [];
        foreach ($ongoingTransactions as $ongoingTransaction) {
            $listing = ProduceListing::find($ongoingTransaction->listing_id);
            $ongoingTransactionsArray[] = <<<EOT
            _________________________
            ID: {$ongoingTransaction->id}
            From: {$ongoingTransaction->from}
            To: {$ongoingTransaction->to}
            Listing:
                Produce: {$listing->produce}
                Quantity: {$listing->quantity}{$listing->unit}
                Price: PHP{$listing->price_per_unit} / {$listing->unit}
                Location: {$listing->location}
            Unit Quantity: {$ongoingTransaction->unit_quantity}{$ongoingTransaction->unit}
            Status: {$ongoingTransaction->status}
            _________________________
            EOT;
        }

        $message = "Ongoing Transactions: \n" . implode("\n", $ongoingTransactionsArray);

        $response = ['success' => true, 'message' => $message];

        return $response;
    }

    protected function closeOngoingTransaction()
    {
        // Expected format for attributes: <ongoing_transaction_id>
        // example: 1

        $from = $this->from;
        $command = $this->command;
        $attributes = $this->attributes;
        $strAttributes = $this->strAttributes;

        Log::info("----------------------------");
        Log::info("user: $command OngoingTransaction $strAttributes");
        Log::info("----------------------------");

        $ongoingTransactionId = intval($attributes[0] ?? 0);
        $ongoingTransaction = OngoingTransactions::find($ongoingTransactionId);

        if (is_null($ongoingTransaction)) {
            return [
                'success' => false,
                'message' => "Invalid request/Ongoing Transaction ID. Please try again.",
            ];
        }

        $listing = ProduceListing::find($ongoingTransaction->listing_id);

        if (is_null($listing)) {
            return [
                'success' => false,
                'message' => "Associated listing with ID {$ongoingTransaction->listing_id} not found.",
            ];
        }

        SmsConversation::create([
            'farmer_phone' => $from,
            'action' => 'close_ongoing_transaction',
            'status' => 'pending',
            'data' => ['ongoing_transaction_id' => $ongoingTransactionId],
        ]);

        $message = <<<EOT
        Closing OngoingTransactionID {$ongoingTransaction->id}...
            From: {$ongoingTransaction->from}
            To: {$listing->farmer_name}
            Listing:
                Produce: {$listing->produce}
                Quantity: {$listing->quantity}{$listing->unit}
                Price: PHP{$listing->price_per_unit} / {$listing->unit}
                Farmer: {$listing->farmer_name}
                Location: {$listing->location}
            Requested Quantity: {$ongoingTransaction->unit_quantity}{$listing->unit}
        Are you sure you want to close this Ongoing Transaction?
        Reply YES or NO.
        EOT;

        $response = ['success' => 'pending', 'message' => $message];

        return $response;
    }

    protected function cancelOngoingTransaction()
    {
        // Expected format for attributes: <ongoing_transaction_id>
        // example: 1

        $from = $this->from;
        $command = $this->command;
        $attributes = $this->attributes;
        $strAttributes = $this->strAttributes;

        Log::info("----------------------------");
        Log::info("user: $command OngoingTransaction $strAttributes");
        Log::info("----------------------------");

        $ongoingTransactionId = intval($attributes[0] ?? 0);
        $ongoingTransaction = OngoingTransactions::find($ongoingTransactionId);

        if (is_null($ongoingTransaction)) {
            return [
                'success' => false,
                'message' => "Invalid request/Ongoing Transaction ID. Please try again.",
            ];
        }

        $listing = ProduceListing::find($ongoingTransaction->listing_id);

        if (is_null($listing)) {
            return [
                'success' => false,
                'message' => "Associated listing with ID {$ongoingTransaction->listing_id} not found.",
            ];
        }

        SmsConversation::create([
            'farmer_phone' => $from,
            'action' => 'cancel_ongoing_transaction',
            'status' => 'pending',
            'data' => ['ongoing_transaction_id' => $ongoingTransactionId],
        ]);

        $message = <<<EOT
        Canceling TransactionRequestID {$ongoingTransaction->id}...
            From: {$ongoingTransaction->from}
            To: {$listing->farmer_name}
            Listing:
                Produce: {$listing->produce}
                Quantity: {$listing->quantity}{$listing->unit}
                Price: PHP{$listing->price_per_unit} / {$listing->unit}
                Farmer: {$listing->farmer_name}
                Location: {$listing->location}
            Requested Quantity: {$ongoingTransaction->unit_quantity}{$listing->unit}
        Are you sure you want to cancel this Ongoing Transaction?
        Reply YES or NO.
        EOT;

        $response = ['success' => 'pending', 'message' => $message];

        return $response;
    }

    public function controlOngoingTransactions(String $from, String $command, array $attributes)
    {
        $this->from = $from;
        $this->farmerName = $attributes[array_search('by', $attributes) + 1] ?? null;
        $this->command = $command;
        $this->attributes = $attributes;
        $this->strAttributes = implode(' ', $attributes);

        if ($command === 'show') {
            // Expected format for attributes: of <of_farmer_name> with <with_farmer_name:nullable> OngoingTransactionId: <ongoing_transaction_id:nullable>
            // example: of Pedro with Juan OngoingTransactionId: 1
            // example: of Pedro OngoingTransactionId: 1
            // example: of Pedro with Juan
            return $this->showOngoingTransactions();
        }

        if ($command === 'close') {
            // Expected format for attributes: <ongoing_transaction_id>
            // example: 1
            return $this->closeOngoingTransaction();
        }

        if ($command === 'cancel') {
            // Expected format for attributes: <ongoing_transaction_id>
            // example: 1
            return $this->cancelOngoingTransaction();
        }
    }
}
