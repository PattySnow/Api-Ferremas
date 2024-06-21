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
        Schema::create('orders_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id'); 
            $table->string('status')->nullable();
            $table->string('vci')->nullable();
            $table->integer('amount')->nullable();
            $table->string('buy_order')->nullable();
            $table->string('session_id')->nullable();
            $table->string('card_number')->nullable();
            $table->string('accounting_date')->nullable();
            $table->dateTime('transaction_date')->nullable();
            $table->string('authorization_code')->nullable();
            $table->string('payment_type_code')->nullable();
            $table->integer('response_code')->nullable();
            $table->integer('installments_amount')->nullable();
            $table->integer('installments_number')->nullable();
            $table->integer('balance')->nullable();
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_details');
    }
};
