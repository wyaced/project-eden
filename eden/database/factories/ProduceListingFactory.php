<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProduceListing>
 */
class ProduceListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $produceArr = ['bigas', 'talong', 'sitaw', 'upo', 'kalabasa', 'labanos', 'mustasa', 'sibuyas', 'kamatis', 'bawang', 'luya', 'kamote', 'patatas'];
        $locations = [
            // Region III - Central Luzon
            // "aurora",
            // "bataan",
            // "bulacan",
            // "nuevaecija",
            // "pampanga",
            // "tarlac",
            // "zambales",
            // "angelescity",
            // "olongapocity",

            // Region IV-A - CALABARZON
            "batangas",
            "cavite",
            "laguna",
            "quezon",
            "rizal",
            "lucenacity",

            // Region V - Bicol Region
            // "albay",
            // "camarinesnorte",
            // "camarinessur",
            // "catanduanes",
            // "masbate",
            // "sorsogon",
            // "nagacity",

            // // NCR - National Capital Region
            // "manila",
            // "caloocan",
            // "laspiñas",
            // "makati",
            // "malabon",
            // "mandaluyong",
            // "marikina",
            // "muntinlupa",
            // "navotas",
            // "parañaque",
            // "pasay",
            // "pasig",
            // "quezoncity",
            // "sanjuan",
            // "taguig",
            // "valenzuela",
            // "pateros"
        ];

        return [
            'farmer_phone' => fake()->phoneNumber(),
            'produce' => fake()->randomElement($produceArr),
            'quantity' => mt_rand(50, 1000),
            'unit' => 'kg',
            'price_per_unit' => mt_rand(20, 400),
            'location' => fake()->randomElement($locations),
            'farmer_name' => fake()->firstName(),
        ];
    }
}
