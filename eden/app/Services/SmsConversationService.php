<?php

namespace App\Services;

use App\Models\OngoingTransactions;
use App\Models\TransactionRequests;
use App\Models\ProduceListing;
use App\Models\SmsConversation;
use App\Services\ProduceService;
use App\Services\TransactionRequestService;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Log;

class SmsConversationService
{
    protected $produceService;
    protected $transactionRequestService;
    protected $twilio;

    public function __construct(ProduceService $produceService, TransactionRequestService $transactionRequestService, TwilioService $twilio)
    {
        $this->produceService = $produceService;
        $this->transactionRequestService = $transactionRequestService;
        $this->twilio = $twilio;
    }

    protected function deleteListingConversation(String $userResponse, array $data)
    {
        $listingId = $data['listing_id'] ?? null;

        if ($userResponse === 'yes') {
            $this->produceService->deleteListing($listingId);
            $message = "ListingId {$listingId} deleted successfully.";
        } elseif ($userResponse === 'no') {
            $message = "Deletion of ListingID {$listingId} cancelled.";
        } else {
            $message = "Invalid response. Please reply with 'yes' or 'no'.";
            return [
                'success' => false,
                'message' => $message,
            ];
        }

        return [
            'success' => true,
            'message' => $message,
        ];
    }

    protected function makeTransactionRequestConversation(String $userResponse, array $data)
    {
        $transactionRequestData = $data['transaction_request_data'] ?? null;

        if ($userResponse === 'yes') {

            if (is_null($transactionRequestData)) {
                return [
                    'success' => false,
                    'message' => "Transaction request data not found. Please try again.",
                ];
            }

            $listing = ProduceListing::find($transactionRequestData['listing_id']);

            $transactionRequestData['to'] = $listing->farmer_name;
            $transactionRequestData['to_phone'] = $listing->farmer_phone;
            $transactionRequestData['status'] = 'pending';

            $transactionRequestResponse = $this->transactionRequestService->makeTransactionRequest($transactionRequestData);
            $transactionRequest = $transactionRequestResponse['transactionRequest'] ?? null;

            if (is_null($transactionRequest)) {
                return [
                    'success' => false,
                    'message' => "Failed to create TransactionRequest. Please try again.",
                ];
            }

            $message = <<<EOT
            TransactionRequestID {$transactionRequest->id} created successfully!
            Request sent to: {$transactionRequest->to}
            For: {$transactionRequest->unit_quantity} of {$listing->produce}
            Request Status: {$transactionRequest->status}
            EOT;

            $messageToSeller = <<<EOT
            You have a new TransactionRequest (ID: {$transactionRequest->id}) for your listing of {$listing->produce}!
            From: {$transactionRequest->from}
            Quantity: {$transactionRequest->unit_quantity} of {$listing->produce}
            Price: PHP{$listing->price_per_unit} / {$listing->unit}
            EOT;

            Log::info("=== (Eden's Notif to Seller Start) ===");
            foreach (explode("\n", $messageToSeller) as $line) {
                Log::info("Eden to Seller: $line");
            }
            Log::info("=== (Eden's Notif to Seller End) ===");

            $this->twilio->sendSms($transactionRequest->to_phone, $messageToSeller);

        } elseif ($userResponse === 'no') {
            $message = "TransactionRequest for ListingID {$transactionRequestData['listing_id']} cancelled.";
        } else {
            $message = "Invalid response. Please reply with 'yes' or 'no'.";
            return [
                'success' => false,
                'message' => $message,
            ];
        }

        return [
            'success' => true,
            'message' => $message,
        ];
    }

    protected function cancelTransactionRequestConversation(String $userResponse, array $data)
    {
        $transactionRequestId = $data['transaction_request_id'] ?? null;

        if ($userResponse === 'yes') {

            if (is_null($transactionRequestId)) {
                return [
                    'success' => false,
                    'message' => "Transaction request data not found. Please try again.",
                ];
            }

            $transactionRequest = TransactionRequests::find($transactionRequestId);

            if (is_null($transactionRequest)) {
                return [
                    'success' => false,
                    'message' => "Transaction Request with ID {$transactionRequestId} not found.",
                ];
            }

            $response = $this->transactionRequestService->cancelTransactionRequest($transactionRequestId);
            return $response;
        } elseif ($userResponse === 'no') {
            $message = "Cancellation of TransactionRequestID {$transactionRequestId} cancelled.";
        } else {
            $message = "Invalid response. Please reply with 'yes' or 'no'.";
            return [
                'success' => false,
                'message' => $message,
            ];
        }

        return [
            'success' => true,
            'message' => $message,
        ];
    }

    protected function acceptTransactionRequestConversation(String $userResponse, array $data)
    {
        $transactionRequestId = $data['transaction_request_id'] ?? null;

        if ($userResponse === 'yes') {

            if (is_null($transactionRequestId)) {
                return [
                    'success' => false,
                    'message' => "Transaction request data not found. Please try again.",
                ];
            }

            $transactionRequest = TransactionRequests::find($transactionRequestId);

            if (is_null($transactionRequest)) {
                return [
                    'success' => false,
                    'message' => "Transaction Request with ID {$transactionRequestId} not found.",
                ];
            }

            $response = $this->transactionRequestService->acceptTransactionRequest($transactionRequestId);

            if ($response['success']) {
                OngoingTransactions::create([
                    'from' => $transactionRequest->from,
                    'from_phone' => $transactionRequest->from_phone,
                    'to' => $transactionRequest->to,
                    'to_phone' => $transactionRequest->to_phone,
                    'listing_id' => $transactionRequest->listing_id,
                    'unit_quantity' => $transactionRequest->unit_quantity,
                    'status' => 'ongoing',
                ]);

                $listing = ProduceListing::find($transactionRequest->listing_id);
                $messageToBuyer = <<<EOT
                Your TransactionRequestID {$transactionRequestId} for {$transactionRequest->unit_quantity} of {$listing->produce} has been accepted by {$transactionRequest->to}!
                Farmer Contact: {$transactionRequest->to} ({$transactionRequest->to_phone})
                EOT;

                Log::info("=== (Eden's Notif to Buyer Start) ===");
                foreach (explode("\n", $messageToBuyer) as $line) {
                    Log::info("Eden to Buyer: $line");
                }
                Log::info("=== (Eden's Notif to Buyer End) ===");

                $this->twilio->sendSms($transactionRequest->from_phone, $messageToBuyer);
            }

            return $response;
        } elseif ($userResponse === 'no') {
            $message = "Acceptance of TransactionRequestID {$transactionRequestId} cancelled.";
        } else {
            $message = "Invalid response. Please reply with 'yes' or 'no'.";
            return [
                'success' => false,
                'message' => $message,
            ];
        }

        return [
            'success' => true,
            'message' => $message,
        ];
    }

    protected function rejectTransactionRequestConversation(String $userResponse, array $data)
    {
        $transactionRequestId = $data['transaction_request_id'] ?? null;

        if ($userResponse === 'yes') {

            if (is_null($transactionRequestId)) {
                return [
                    'success' => false,
                    'message' => "Transaction request data not found. Please try again.",
                ];
            }

            $transactionRequest = TransactionRequests::find($transactionRequestId);

            if (is_null($transactionRequest)) {
                return [
                    'success' => false,
                    'message' => "Transaction Request with ID {$transactionRequestId} not found.",
                ];
            }

            $response = $this->transactionRequestService->rejectTransactionRequest($transactionRequestId);

            if ($response['success']) {
                $listing = ProduceListing::find($transactionRequest->listing_id);
                $messageToBuyer = <<<EOT
                Your TransactionRequestID {$transactionRequestId} for {$transactionRequest->unit_quantity} of {$listing->produce} has been rejected by {$transactionRequest->to}.
                EOT;

                Log::info("=== (Eden's Notif to Buyer Start) ===");
                foreach (explode("\n", $messageToBuyer) as $line) {
                    Log::info("Eden to Buyer: $line");
                }
                Log::info("=== (Eden's Notif to Buyer End) ===");

                $this->twilio->sendSms($transactionRequest->from_phone, $messageToBuyer);
            }

            return $response;
        } elseif ($userResponse === 'no') {
            $message = "Rejection of TransactionRequestID {$transactionRequestId} cancelled.";
        } else {
            $message = "Invalid response. Please reply with 'yes' or 'no'.";
            return [
                'success' => false,
                'message' => $message,
            ];
        }

        return [
            'success' => true,
            'message' => $message,
        ];
    }

    public function controlConversations(SmsConversation $conversation, String $userResponse)
    {
        $action = $conversation->action;
        $data = $conversation->data;

        Log::info("----------------------------");
        Log::info("user: $userResponse");
        Log::info("----------------------------");

        if ($action === 'delete_listing') {
            $conversationResponse = $this->deleteListingConversation($userResponse, $data);
        }

        if ($action === 'make_transaction_request') {
            $conversationResponse = $this->makeTransactionRequestConversation($userResponse, $data);
        }

        if ($action === 'cancel_transaction_request') {
            $conversationResponse = $this->cancelTransactionRequestConversation($userResponse, $data);
        }

        if ($action === 'accept_transaction_request') {
            $conversationResponse = $this->acceptTransactionRequestConversation($userResponse, $data);
        }

        if ($action === 'reject_transaction_request') {
            $conversationResponse = $this->rejectTransactionRequestConversation($userResponse, $data);
        }

        if ($conversationResponse['success']) {
            $conversation->update(['status' => 'completed']);
        } elseif ($conversationResponse['success'] === false) {
            $conversationResponse['success'] = 'pending';
        }

        return $conversationResponse ?? ['success' => false, 'message' => "Invalid conversation action."];
    }
}
