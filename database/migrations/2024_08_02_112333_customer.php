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
        Schema::table('customer', function($table) {
            $table->decimal('balance', 18, 2)->default('0.00')->after('mobile');
            $table->string('vat')->nullable()->after('gst');
            $table->string('fax')->nullable()->after('vat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
