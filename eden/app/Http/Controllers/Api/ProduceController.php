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

    public function showListings(
        array $showRequest = ['produce' => null, 'farmer_name' => null, 'location' => null],
        String $orderDirection = 'asc'
    ) {
        return response()->json($this->produceService->showListings($showRequest, $orderDirection));
    }
}
