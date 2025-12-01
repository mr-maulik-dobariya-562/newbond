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
        Schema::create('monthly_report_checks', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->foreignId('created_by')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade')->nullable();
            $table->foreignId('branch_id')->references('id')->on('branches')->onDelete('cascade')->onUpdate('cascade')->nullable();
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
        Schema::dropIfExists('monthly_report_checks');
    }
};
