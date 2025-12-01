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
        Schema::table('estimates', function (Blueprint $table) {
            $table->string('estimate_code')->nullable()->after('customer_id');
            $table->longText('note')->nullable()->after('discription');
            $table->foreignId('bill_group_id')->nullable()->references('id')->on('bill_groups')->onDelete('cascade')->onUpdate('cascade')->after('note');
            $table->enum('bill_generated', ['Yes', 'No'])->default('No')->after('bill_group_id');
            $table->foreignId('courier_id')->nullable()->references('id')->on('couriers')->onDelete('cascade')->onUpdate('cascade')->after('bill_generated');
            $table->foreignId('transport_id')->nullable()->references('id')->on('transports')->onDelete('cascade')->onUpdate('cascade')->after('courier_id');
            $table->foreignId('invoice_id')->nullable()->references('id')->on('invoice_types')->onDelete('cascade')->onUpdate('cascade')->after('bill_group_id');
            $table->string('lr_no')->nullable()->after('invoice_id');
            $table->date('lr_date')->nullable()->after('lr_no');
            $table->string('docket')->nullable()->after('lr_date');
            $table->string('parcel')->nullable()->after('docket');
            $table->string('other_charge')->nullable()->after('net_amount');
            $table->string('discount_amount',15,2)->nullable()->after('discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('estimate', function (Blueprint $table) {
            //
        });
    }
};
