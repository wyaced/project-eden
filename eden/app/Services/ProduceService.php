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

    public function getProduceNames()
    {
        $listings = ProduceListing::select('produce')->distinct()->get();
        $names = [];
        foreach($listings as $listing) {
            $names[] = $listing->produce;
        }

        return $names;
    }

    public function getLocations()
    {
        $listings = ProduceListing::select('location')->distinct()->get();
        $names = [];
        foreach($listings as $listing) {
            $names[] = $listing->location;
        }

        return $names;
    }

    public function showListings(array $showRequest, String $orderDirection = 'asc')
    {
        $query = ProduceListing::query();

        if (!is_null($showRequest['produce']) && $showRequest['produce'] != "all") {
            $query->where('produce', 'like', '%' . $showRequest['produce'] . '%');
        }

        if (!is_null($showRequest['farmer_name'])) {
            $query->where('farmer_name', 'like', '%' . $showRequest['farmer_name'] . '%');
        }

        if (!is_null($showRequest['location']) && $showRequest['location'] != "all") {
            $query->where('location', 'like', '%' . $showRequest['location'] . '%');
        }

        return $query->orderBy('price_per_unit', $orderDirection)->get();
    }

    public function updateListing(ProduceListing $produceListing, array $updateRequest)
    {
        $produceListing->update($updateRequest);
        return ['success' => true, 'listing' => $produceListing];
    }

    public function deleteListing($id)
    {
        $produceListing = ProduceListing::find($id);
        if (!$produceListing) {
            return ['success' => false];
        }
        return ['success' => $produceListing->delete()];
    }
}