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
        Schema::table('items', function (Blueprint $table) {
            $table->foreignId('local_size_id')->nullable()->references('id')->on('cartoons')->onDelete('cascade')->onUpdate('cascade')->after('usd_old_price');
            $table->foreignId('export_size_id')->nullable()->references('id')->on('cartoons')->onDelete('cascade')->onUpdate('cascade')->after('local_size_id');
            $table->string('export_weight')->nullable()->after('export_size_id');
            $table->string('local_weight')->nullable()->after('local_size_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            //
        });
    }
};
