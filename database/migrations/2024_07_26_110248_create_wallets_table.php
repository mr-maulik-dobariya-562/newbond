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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['CREDIT', 'DEBIT']);
            $table->decimal('balance', 15, 2);
            $table->string('remark')->nullable();
            $table->integer('ref_id')->nullable();
            $table->date('date');
            $table->foreignId('txn_type_id')->references('id')->on('txn_types')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('user_id')->nullable();
            $table->datetime("created_at")->useCurrent();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->decimal('balance', 15, 2)->default(0)->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
