<?php

namespace App\Services;

use App\Models\OngoingTransactions;

class OngoingTransactionService
{
    public function showOngoingTransactions(array $showRequest, String $orderDirection = 'asc')
    {
        $query = OngoingTransactions::query();

        $query->where(function ($q) use ($showRequest) {

            $q->where(function ($sub) use ($showRequest) {
                $sub->where('from', $showRequest['of_farmer_name']);

                if (!is_null($showRequest['with_farmer_name'])) {
                    $sub->where('to', 'like', '%' . $showRequest['with_farmer_name'] . '%');
                }
            })

                ->orWhere(function ($sub) use ($showRequest) {
                    $sub->where('to', $showRequest['of_farmer_name']);

                    if (!is_null($showRequest['with_farmer_name'])) {
                        $sub->where('from', 'like', '%' . $showRequest['with_farmer_name'] . '%');
                    }
                });
        });

        if (!is_null($showRequest['ongoing_transaction_id'])) {
            $query->where('id', $showRequest['ongoing_transaction_id']);
        }

        return $query->where('status', 'ongoing')
            ->whereNotNull('listing_id')
            ->orderBy('updated_at', $orderDirection)->get();
    }

    public function closeOngoingTransaction($ongoingTransactionId)
    {
        $ongoingTransaction = OngoingTransactions::find($ongoingTransactionId);

        if (is_null($ongoingTransaction)) {
            return [
                'success' => false,
                'message' => "Invalid request/Ongoing Transaction ID. Please try again.",
            ];
        }

        $ongoingTransaction->update(['status' => 'closed']);

        $message = "OngoingTransactionID {$ongoingTransactionId} closed successfully!";
        return ['success' => true, 'message' => $message];
    }

    public function cancelOngoingTransaction($ongoingTransactionId)
    {
        $ongoingTransaction = OngoingTransactions::find($ongoingTransactionId);

        if (is_null($ongoingTransaction)) {
            return [
                'success' => false,
                'message' => "Invalid request/Ongoing Transaction ID. Please try again.",
            ];
        }

        $ongoingTransaction->update(['status' => 'canceled']);

        $message = "OngoingTransactionID {$ongoingTransactionId} canceled successfully!";
        return ['success' => true, 'message' => $message];
    }
}
