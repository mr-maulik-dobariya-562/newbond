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
        Schema::create('item_group', function (Blueprint $table) {
            $table->id();
            $table->string('group_name');
            $table->string('item_group')->nullable();
            $table->decimal('sequence_number', 15, 2)->default(0);
            $table->string('gst')->nullable();
            $table->foreignId('case_type_id')->nullable()->references('id')->on('case_types')->onDelete('cascade')->onUpdate('cascade');
            $table->enum('retail_wp_available', ['YES', 'NO'])->default('NO');
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
        Schema::dropIfExists('item_group');
    }
};
