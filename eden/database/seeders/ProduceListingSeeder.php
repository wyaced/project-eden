<?php

namespace Database\Seeders;

use App\Models\ProduceListing;
use App\Services\MarketMovementsService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProduceListingSeeder extends Seeder
{
    protected $marketMovementService;

    public function __construct(MarketMovementsService $marketMovementsService)
    {
        $this->marketMovementService = $marketMovementsService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProduceListing::factory()->count(100)->create();
        $this->marketMovementService->recordMarketMovement();
    }
}
