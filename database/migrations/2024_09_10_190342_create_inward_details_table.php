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
        Schema::create('inward_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inward_id')->references('id')->on('inwards')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('item_id')->references('id')->on('items')->onDelete('cascade')->onUpdate('cascade');

            $table->string('qty', 15, 2)->default('0');
            $table->string('remark')->nullable();

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
        Schema::dropIfExists('inward_details');
    }
};
