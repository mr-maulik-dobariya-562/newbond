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
        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->references('id')->on('customer')->onDelete('cascade')->onUpdate('cascade');
            $table->longText('address')->nullable();
            $table->string('company_name')->nullable();
            $table->string('discription')->nullable();
            $table->string('po_no')->nullable();
            $table->date('date')->nullable();

            $table->string('lr_photo')->nullable();

            $table->date('payment_date')->nullable();
            $table->string('payment_amount',15,2)->default('0');
            $table->string('comments')->nullable();
            $table->string('is_verified')->default('0');
            $table->string('total_amount',15,2)->default('0');
            $table->string('discount',15,2)->default('0');
            $table->string('redeem_coin')->nullable();
            $table->string('net_amount',15,2)->default('0');
            $table->string('cash_back_coin')->nullable();
            $table->string('offer_discount')->nullable();
            $table->string('estimate_type')->nullable();
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('updated_by')->nullable()->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->datetime("created_at")->useCurrent();
            $table->datetime("updated_at")->useCurrentOnUpdate()->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimates');
    }
};
