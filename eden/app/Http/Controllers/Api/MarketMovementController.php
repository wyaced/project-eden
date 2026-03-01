<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarketMovement;
use App\Services\MarketMovementsService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class MarketMovementController extends Controller
{
    protected $marketMovementService;

    public function __construct(MarketMovementsService $marketMovementsService)
    {
        $this->marketMovementService = $marketMovementsService;
    }

    public function getMarketMovementRecords(String $type, String $produce, ?String $location = null)
    {
        if ($location === "all") $location = null;
        if ($type === 'price') {
            $response = $this->marketMovementService->getPriceMovementRecords($produce, $location);
        }

        if ($type === 'supply') {
            $response = $this->marketMovementService->getSupplyMovementRecords($produce, $location);
        }

        return response()->json($response);
    }

    public function getMarketMovement()
    {
        return response()->json($this->marketMovementService->getMarketMovement());
    }

    public function recordMarketMovement()
    {
        $this->marketMovementService->recordMarketMovement();
    }
}
