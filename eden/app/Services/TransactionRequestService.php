<?php

namespace App\Services;

use App\Models\ProduceListing;
use App\Models\TransactionRequests;

class TransactionRequestService
{
    public function makeTransactionRequest(array $transactionRequestData)
    {
        $transactionRequest = new TransactionRequests($transactionRequestData);
        return ['success' => $transactionRequest->save(), 'transactionRequest' => $transactionRequest];
    }
}