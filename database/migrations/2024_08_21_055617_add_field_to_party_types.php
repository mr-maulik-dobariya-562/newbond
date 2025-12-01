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
        Schema::table('party_types', function (Blueprint $table) {
            $table->enum('item_price', ['Dealer', 'Retailer', 'USD'])->default('Dealer')->after('name');
            $table->enum('extra_price', ['INR', 'USD', 'NON'])->default('NON')->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('party_types', function (Blueprint $table) {
            //
        });
    }
};
