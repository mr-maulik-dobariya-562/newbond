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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('categories_id')->nullable()->references('id')->on('item_categories')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('extra_retail_discount',15,2)->default('0');
            $table->decimal('extra_dealer_discount',15,2)->default('0');
            $table->decimal('dealer_current_price',15,2)->default('0');
            $table->decimal('retail_current_price',15,2)->default('0');
            $table->decimal('dealer_old_price',15,2)->default('0');
            $table->decimal('retail_old_price',15,2)->default('0');
            $table->enum('type', ['Finish', 'Raw', 'Semi-finished'])->default('Finish');
            $table->enum('active_type', ['Active', 'Non Active', 'Offline'])->default('Active');
            $table->string('packing');
            $table->integer('minimum_qty');
            $table->string('image')->nullable();
            $table->foreignId('created_by')->nullable()->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('items');
    }
};
