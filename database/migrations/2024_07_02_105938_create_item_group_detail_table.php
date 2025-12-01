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
        Schema::create('item_group_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('print_type_id')->references('id')->on('print_type')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('item_group_id')->references('id')->on('item_group')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('min_dealer', 15, 2)->nullable();
            $table->decimal('total_dealer', 15, 2)->nullable();
            $table->decimal('min_retail', 15, 2)->nullable();
            $table->decimal('total_retail', 15, 2)->nullable();
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
        Schema::dropIfExists('item_group_detail');
    }
};
