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
        Schema::create('market_movements', function (Blueprint $table) {
            $table->id();

            $table->string('produce');
            $table->string('location');
            $table->integer('total_local_unit_quantity');
            $table->decimal('avg_local_price_per_unit', 8, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_movements');
    }
};
