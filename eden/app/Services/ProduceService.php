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

    public function showListings(array $showRequest)
    {
        $query = ProduceListing::query();

        if (!is_null($showRequest['produce'])) {
            $query->where('produce', 'like', '%' . $showRequest['produce'] . '%');
        }

        if (!is_null($showRequest['farmer_name'])) {
            $query->where('farmer_name', 'like', '%' . $showRequest['farmer_name'] . '%');
        }

        if (!is_null($showRequest['location'])) {
            $query->where('location', 'like', '%' . $showRequest['location'] . '%');
        }

        return $query->get();
    }
}