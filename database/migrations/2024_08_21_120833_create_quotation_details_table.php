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
        Schema::create('quotation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->references('id')->on('items')->onDelete('cascade')->onUpdate('cascade');
            $table->string('item_name')->nullable();
            $table->foreignId('print_type_id')->references('id')->on('print_type')->onDelete('cascade')->onUpdate('cascade');
            $table->string('qty',15,2)->default('0');
            $table->string('rate',15,2)->default('0');
            $table->string('dispatch_qty',15,2)->default('0');
            $table->enum('block', ['NEW', 'CHANG', 'OLD'])->default('OLD');
            $table->enum('status', ['Sale', 'Non Sale'])->default('Non Sale');
            $table->string('narration')->nullable();
            $table->string('remark')->nullable();
            $table->foreignId('transport_id')->references('id')->on('transports')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->string('amount',15,2)->default('0');
            $table->date('date')->nullable();
            $table->string('design')->nullable();
            $table->decimal('discount',15,2)->default('0');
            $table->foreignId('quotation_id')->references('id')->on('quotations')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('quotation_details');
    }
};
