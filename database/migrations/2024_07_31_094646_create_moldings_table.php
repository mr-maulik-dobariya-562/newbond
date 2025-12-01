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
        Schema::create('moldings', function (Blueprint $table) {
            $table->id();
            $table->date("date");
            $table->foreignId('shift_id')->references('id')->on('shifts')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('machine_id')->references('id')->on('machines')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('operator_id')->references('id')->on('customer')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('product_type_id')->references('id')->on('product_types')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('row_material_id')->references('id')->on('row_materials')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('cavity_id')->references('id')->on('cavities')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('item_id')->references('id')->on('items')->onDelete('cascade')->onUpdate('cascade');
            $table->string("machine_counter");
            $table->string("production_weight");
            $table->string("production_pieces_quantity");
            $table->string("runner_waste");
            $table->string("component_rejection");
            $table->string("color_type");
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
        Schema::dropIfExists('moldings');
    }
};
