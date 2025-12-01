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
        Schema::create('printings', function (Blueprint $table) {
            $table->id();
            $table->date("date");
            $table->foreignId('print_type_id')->references('id')->on('print_type')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('machine_id')->references('id')->on('machines')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->foreignId('operator_id')->references('id')->on('customer')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->string("production_qty")->nullable();
            $table->string("rection_qty")->nullable();
            $table->foreignId('working_hours_id')->references('id')->on('working_hours')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->string("rejection_qty")->nullable();
            $table->string("rejection_reason")->nullable();
            $table->string("remarks")->nullable();
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
        Schema::dropIfExists('printings');
    }
};
