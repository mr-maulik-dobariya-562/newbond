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
        Schema::create('print_group_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('print_type_id')->references('id')->on('print_type')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->foreignId('item_group_id')->references('id')->on('item_group')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->string('image')->nullable();
            $table->foreignId('created_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade')->nullable();
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
        Schema::dropIfExists('print_group_images');
    }
};
