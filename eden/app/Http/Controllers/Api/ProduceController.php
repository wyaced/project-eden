<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ProduceService;
use Illuminate\Http\Request;

class ProduceController extends Controller
{
    protected $produceService;

    public function __construct(ProduceService $produceService)
    {
        $this->produceService = $produceService;
    }

    public function getProduceNames()
    {
        return response()->json($this->produceService->getProduceNames());
    }

    public function getLocations()
    {
        return response()->json($this->produceService->getLocations());
    }

    public function showListings(?String $produce = null, ?String $location = null, String $orderDirection = 'asc')
    {
        $showRequest = ['produce' => $produce, 'farmer_name' => null, 'location' => $location];
        return response()->json($this->produceService->showListings($showRequest, $orderDirection));
    }
}
