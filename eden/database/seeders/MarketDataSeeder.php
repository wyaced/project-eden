<?php

namespace Database\Seeders;

use App\Models\OngoingTransactions;
use App\Models\ProduceListing;
use App\Models\TransactionRequests;
use App\Services\MarketMovementsService;
use App\Services\OngoingTransactionService;
use App\Services\TransactionRequestService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class MarketDataSeeder extends Seeder
{
    protected $marketMovementService;
    protected $transactionRequestService;
    protected $ongoingTransactionService;

    public function __construct(MarketMovementsService $marketMovementsService, TransactionRequestService $transactionRequestService, OngoingTransactionService $ongoingTransactionService)
    {
        $this->marketMovementService = $marketMovementsService;
        $this->transactionRequestService = $transactionRequestService;
        $this->ongoingTransactionService = $ongoingTransactionService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProduceListing::factory()->count(100)->create();
        $this->marketMovementService->recordMarketMovement();
        $listingSample = ProduceListing::take(ProduceListing::count() * 0.5)->get();
        foreach($listingSample as $listing) {
            Log::info(json_encode($listing));
            $transactionRequest = [
                'from' => 'Juan',
                'from_phone' => '+1234567891',
                'to' => $listing->farmer_name,
                'to_phone' => $listing->farmer_phone,
                'listing_id' => $listing->id,
                'unit_quantity' => $listing->quantity > 0 ? mt_rand(1, $listing->quantity) : 0
            ];
            $this->transactionRequestService->makeTransactionRequest($transactionRequest);
        }
        $transactionRequests = TransactionRequests::where('status', 'pending')->get();
        foreach($transactionRequests as $transactionRequest) {
            Log::info(json_encode($transactionRequest));
            $this->transactionRequestService->acceptTransactionRequest($transactionRequest->id);
        }
        $ongoingTransactions = OngoingTransactions::where('status', 'ongoing')->get();
        foreach($ongoingTransactions as $ongoingTransaction) {
            Log::info(json_encode($ongoingTransaction));
            $this->ongoingTransactionService->closeOngoingTransaction($ongoingTransaction->id);
        }
        $this->marketMovementService->recordMarketMovement();
    }
}
