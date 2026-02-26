<?php

namespace App\Services;

use App\Models\ProduceListing;
use App\Models\SmsConversation;
use App\Services\ProduceService;
use App\Services\TransactionRequestService;
use Illuminate\Support\Facades\Log;

class SmsConversationService
{
    protected $produceService;
    protected $transactionRequestService;

    public function __construct(ProduceService $produceService, TransactionRequestService $transactionRequestService)
    {
        $this->produceService = $produceService;
        $this->transactionRequestService = $transactionRequestService;
    }

    protected function deleteListingConversation(String $userResponse, array $data)
    {
        if ($userResponse === 'yes') {
            $this->produceService->deleteListing($data['listing_id']);
            $message = "ListingId {$data['listing_id']} deleted successfully.";
        } elseif ($userResponse === 'no') {
            $message = "Deletion of ListingID {$data['listing_id']} cancelled.";
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
            Request sent to: {$transactionRequest->to} ({$transactionRequest->to_phone})
            For: {$transactionRequest->unit_quantity} of {$listing->produce}
            Request Status: {$transactionRequest->status}
            EOT;
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

        if ($conversationResponse['success']) {
            $conversation->update(['status' => 'completed']);
        } elseif ($conversationResponse['success'] === false) {
            $conversationResponse['success'] = 'pending';
        }

        return $conversationResponse ?? ['success' => false, 'message' => "Invalid conversation action."];
    }
}
