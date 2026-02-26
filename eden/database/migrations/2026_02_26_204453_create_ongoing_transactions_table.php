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
        Schema::create('ongoing_transactions', function (Blueprint $table) {
            $table->id();
           
            $table->string('from');
            $table->string('from_phone');
            $table->string('to');
            $table->string('to_phone');
            $table->string('listing_id')->constrained('listings')->onDelete('cascade');
            $table->integer('unit_quantity');
            $table->string('status')->default('ongoing');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ongoing_transactions');
    }
};
