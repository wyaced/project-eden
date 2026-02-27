<?php

namespace App\Services;

use App\Models\TransactionRequests;

class TransactionRequestService
{
    public function makeTransactionRequest(array $transactionRequestData)
    {
        $transactionRequest = new TransactionRequests($transactionRequestData);
        return ['success' => $transactionRequest->save(), 'transactionRequest' => $transactionRequest];
    }

    public function showTransactionRequests(array $showRequest, String $orderDirection = 'asc')
    {
        $query = TransactionRequests::query();

        if (!is_null($showRequest['for_farmer_name'])) {
            $query->where('to', 'like', '%' . $showRequest['for_farmer_name'] . '%');
        }

        if (!is_null($showRequest['from_farmer_name'])) {
            $query->where('from', 'like', '%' . $showRequest['from_farmer_name'] . '%');
        }

        if (!is_null($showRequest['transaction_request_id'])) {
            $query->where('id', $showRequest['transaction_request_id']);
        }

        return $query->where('status', 'accepted')
            ->orwhere('status', 'pending')
            ->orWhere('status', 'rejected')
            ->whereNotNull('listing_id')
            ->orderBy('updated_at', $orderDirection)->get();
    }

    public function cancelTransactionRequest($transactionRequestId)
    {
        $transactionRequest = TransactionRequests::find($transactionRequestId);

        if (is_null($transactionRequest)) {
            return [
                'success' => false,
                'message' => "Invalid request/Transaction Request ID. Please try again.",
            ];
        }

        $transactionRequest->update(['status' => 'cancelled']);

        $message = "TransactionRequestID {$transactionRequestId} cancelled successfully!";
        return ['success' => true, 'message' => $message];
    }

    public function acceptTransactionRequest($transactionRequestId)
    {
        $transactionRequest = TransactionRequests::find($transactionRequestId);

        if (is_null($transactionRequest)) {
            return [
                'success' => false,
                'message' => "Invalid request/Transaction Request ID. Please try again.",
            ];
        }

        $transactionRequest->update(['status' => 'accepted']);

        $message = "TransactionRequestID {$transactionRequestId} accepted successfully!";
        return ['success' => true, 'message' => $message];
    }

    public function rejectTransactionRequest($transactionRequestId)
    {
        $transactionRequest = TransactionRequests::find($transactionRequestId);

        if (is_null($transactionRequest)) {
            return [
                'success' => false,
                'message' => "Invalid request/Transaction Request ID. Please try again.",
            ];
        }

        $transactionRequest->update(['status' => 'rejected']);

        $message = "TransactionRequestID {$transactionRequestId} rejected successfully!";
        return ['success' => true, 'message' => $message];
    }
}
