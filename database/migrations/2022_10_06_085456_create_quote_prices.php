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
        Schema::create('quote_prices', function (Blueprint $table) {
            $table->id();
            $table->string('quoted_price');
            $table->string('extra_charges')->nullable();
            $table->string('reason_for_extra_charges')->nullable();
            $table->string('any_other_extra_charges')->nullable();
            $table->string('tax_amount')->nullable();
            $table->string('other')->nullable();
            $table->string('description')->nullable();
            $table->string('slug')->nullable();
            $table->tinyInteger('status')->default(1);//1:active, 0:in-active 2:history
            $table->tinyInteger('quote_price_for')->default(1);//1:customer, 2:Sub
            $table->unsignedBigInteger('quoted_uid');
            $table->foreign('quoted_uid')->references('id')->on('users');
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
        Schema::dropIfExists('quote_prices');
    }
};
