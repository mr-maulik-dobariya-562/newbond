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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->longText('remark')->nullable();
            $table->enum('type', ['CREDIT', 'DEBIT']);
            $table->enum('payment_type', ['CASH', 'CREDIT_CARD', 'DEBIT_CARD', 'VOUCHER', 'CHEQUE']);
            $table->string('number')->nullable();
            $table->foreignId('bank_id')->references('id')->on('banks')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->foreignId('customer_id')->references('id')->on('customer')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('created_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('payments');
    }
};
