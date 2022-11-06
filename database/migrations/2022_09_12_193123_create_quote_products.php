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
        Schema::create('quote_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_name')->nullable();
            $table->string('product_id')->nullable();
            $table->string('quantity')->nullable();
            $table->string('size')->nullable();
            $table->string('size_slug')->nullable();
            $table->string('size_unit')->nullable();
            $table->text('description')->nullable();

            $table->unsignedBigInteger('cat_id');
            $table->foreign('cat_id')->references('id')->on('product_categories');
            $table->unsignedBigInteger('quote_id');
            $table->foreign('quote_id')->references('id')->on('quotes');
            
            $table->unsignedBigInteger('pickup_dropoff_id');
            $table->foreign('pickup_dropoff_id')->references('id')->on('pickup_dropoff_address');
            $table->tinyInteger('pickup_dropoff_order_number')->default(1)->nullable();

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
        Schema::dropIfExists('quote_products');
    }
};
