<?php

namespace Database\Seeders;

use App\Models\ProduceListing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProduceListingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProduceListing::factory()->count(100)->create();
    }
}
