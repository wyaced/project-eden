<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produce_listings', function (Blueprint $table) {
            $table->id();

            $table->string('farmer_phone');
            $table->string('produce');
            $table->integer('quantity');
            $table->string('unit');
            $table->decimal('price_per_unit', 8, 2);
            $table->string('location');
            $table->string('farmer_name')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produce_listings');
    }
};
