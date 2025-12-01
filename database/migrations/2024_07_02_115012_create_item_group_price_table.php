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
        Schema::create('item_group_price', function (Blueprint $table) {
            $table->id();
            $table->foreignId('print_type_id')->references('id')->on('print_type')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('item_group_id')->references('id')->on('item_group')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('extra_price', 15, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_group_price');
    }
};
