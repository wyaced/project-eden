<?php

namespace App\Services;

use App\Models\ProduceListing;

class ProduceService
{
    public function createListing(array $listingData)
    {
        $produceListing = new ProduceListing($listingData);
        return ['success' => $produceListing->save(), 'listing' => $produceListing];
    }
}