<?php

namespace App\Services;

use App\Models\MarketMovement;
use App\Models\ProduceListing;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class MarketMovementsService
{
    public function getSupplyMovementRecords(String $produce, ?String $location = null)
    {
        $records = MarketMovement::where('produce', $produce)
            ->orderBy('created_at')
            ->get();

        $timeGroups = [];

        foreach ($records as $record) {
            $timestamp = (string) $record->created_at;

            if (!isset($timeGroups[$timestamp])) {
                $timeGroups[$timestamp] = [];
            }

            // Key = location, value = total quantity (or price)
            $timeGroups[$timestamp][$record->location] = $record->total_local_unit_quantity;
        }

        // Transform into array suitable for JSON
        $result = [];
        foreach ($timeGroups as $time => $locations) {
            $result[] = [
                'timestamp' => CarbonImmutable::parse($time)->timestamp * 1000,
                ...$locations
            ];
        }

        return $result;
    }

    public function getPriceMovementRecords(String $produce, ?String $location = null)
    {
        $records = MarketMovement::where('produce', $produce)
            ->orderBy('created_at')
            ->get();

        $timeGroups = [];

        foreach ($records as $record) {
            $timestamp = (string) $record->created_at;

            if (!isset($timeGroups[$timestamp])) {
                $timeGroups[$timestamp] = [];
            }

            // Key = location, value = total quantity (or price)
            $timeGroups[$timestamp][$record->location] = $record->avg_local_price_per_unit;
        }

        // Transform into array suitable for JSON
        $result = [];
        foreach ($timeGroups as $time => $locations) {
            $result[] = [
                'timestamp' => CarbonImmutable::parse($time)->timestamp * 1000,
                ...$locations
            ];
        }

        return $result;
    }

    public function getMarketMovement()
    {
        $query = ProduceListing::query();

        $query->selectRaw('produce, location, SUM(quantity) as total_local_unit_quantity, AVG(price_per_unit) as avg_local_price_per_unit')
            ->groupBy('produce')
            ->groupBy('location');

        return $query->get();
    }

    public function recordMarketMovement()
    {
        $records = $this->getMarketMovement();

        $now = now();

        foreach ($records as $record) {
            MarketMovement::create([
                'produce' => $record->produce,
                'location' => $record->location,
                'total_local_unit_quantity' => $record->total_local_unit_quantity,
                'avg_local_price_per_unit' => $record->avg_local_price_per_unit,
                'created_at' => $now,
                'updated_at' => $now
            ]);
        }
    }
}
