<?php

use App\Models\TxnType;
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
        Schema::create('txn_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        
        TxnType::insert([
            ['name' => 'Order'],
            ['name' => 'Referral'],
            ['name' => 'Redeem'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('txn_types');
    }
};
