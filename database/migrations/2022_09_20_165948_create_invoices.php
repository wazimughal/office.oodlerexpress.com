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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->nullable();
            $table->string('payee_name')->nullable();
            $table->string('payee_phone')->nullable();
            $table->text('description')->nullable();
            $table->string('paid_amount');
            $table->string('tax_amount')->nullable();
            $table->string('other')->nullable();
            $table->string('slug')->nullable();
            $table->tinyInteger('created_by')->nullable(0);
            $table->unsignedBigInteger('quote_id');
            $table->foreign('quote_id')->references('id')->on('quotes');
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
        Schema::dropIfExists('invoices');
    }
};
