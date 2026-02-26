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
        Schema::table('transaction_requests', function (Blueprint $table) {
            $table->string('from_phone')->after('from');
            $table->string('to_phone')->after('to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_requests', function (Blueprint $table) {
            $table->dropColumn('from_phone');
            $table->dropColumn('to_phone');
        });
    }
};
