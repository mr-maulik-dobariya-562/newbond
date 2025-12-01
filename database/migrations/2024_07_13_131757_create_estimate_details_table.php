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
        Schema::create('estimate_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->references('id')->on('items')->onDelete('cascade')->onUpdate('cascade');
            $table->string('item_name')->nullable();
            $table->foreignId('print_type_id')->nullable()->references('id')->on('print_type')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('print_type_other_id')->nullable()->references('id')->on('print_type_extra')->onDelete('cascade')->onUpdate('cascade');
            $table->string('qty',15,2)->default('0');
            $table->string('rate',15,2)->default('0');
            $table->enum('block', ['NEW', 'CHANG', 'OLD'])->default('OLD');
            $table->string('narration')->nullable();
            $table->string('remark')->nullable();
            $table->string('other_remark')->nullable();
            $table->foreignId('transport_id')->references('id')->on('transports')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->string('amount',15,2)->default('0');
            $table->date('date')->nullable();
            $table->string('design')->nullable();
            $table->integer('discount')->default('0');
            $table->foreignId('estimate_id')->references('id')->on('estimates')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('order_id')->nullable()->references('id')->on('orders')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('estimate_details');
    }
};
