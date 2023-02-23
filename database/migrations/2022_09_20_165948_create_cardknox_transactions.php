<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cardknox_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('xcardnum')->nullable();
            $table->string('xcardexp')->nullable();
            $table->string('xcardcvv')->nullable();
            $table->string('xcardtype')->nullable();
            $table->string('payee_name')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('xdate')->nullable();
            $table->text('xrefnum')->nullable();
            $table->string('paid_amount');
            $table->text('other')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('invoice_id')->nullable();
            $table->tinyInteger('qb_payment_status')->default(0);
            $table->string('transaction_timestamp_id')->nullable();
            $table->unsignedBigInteger('quote_id');
            $table->foreign('quote_id')->references('id')->on('quotes');
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cardknox_transactions');
    }
};
