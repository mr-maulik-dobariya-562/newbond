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
        Schema::create('customer', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();
            $table->string('country_id')->nullable();
            $table->string('state_id')->nullable();
            $table->string('city_id')->nullable();
            $table->string('address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('area')->nullable();
            $table->string('pincode')->nullable();
            $table->string('password')->nullable();
            $table->string('pay_terms')->nullable();
            $table->unsignedBigInteger('party_type_id');
            $table->foreign('party_type_id')->references('id')->on('party_types')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('party_group_id');
            $table->foreign('party_group_id')->references('id')->on('party_groups')->onDelete('cascade')->onUpdate('cascade');
            $table->string('bill_type')->nullable();
            $table->string('gst')->nullable();
            $table->string('pan_no')->nullable();
            $table->string('price')->nullable();
            $table->string('discount')->nullable();
            $table->enum('status', ['ACTIVE', 'INACTIVE', 'OFFLINE'])->default('ACTIVE');
            /* Other */
            $table->string('other_address')->nullable();
            $table->string('other_state_id')->nullable();
            $table->string('other_city_id')->nullable();
            $table->string('other_pincode')->nullable();
            $table->enum('other_sample', ['YES', 'NO'])->default('NO');
            $table->string('other_courier_id')->nullable();
            $table->string('other_transport_id')->nullable();
            $table->string('other_reason_remark')->nullable();
            $table->string('reference')->nullable();
            $table->string('token_id')->nullable();
            $table->date('last_login_date')->nullable();

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
        Schema::dropIfExists('customer');
    }
};
