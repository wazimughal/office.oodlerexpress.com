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
        Schema::create('pickup_dropoff_address', function (Blueprint $table) {
            $table->id();
            $table->string('pickup_street_address')->nullable();
            $table->string('pickup_unit')->nullable();
            $table->string('pickup_state')->nullable();
            $table->string('pickup_city')->nullable();
            $table->string('pickup_zipcode')->nullable();
            $table->string('pickup_contact_number')->nullable();
            $table->text('pickup_date')->nullable();
            $table->tinyInteger('pickup_at_time')->default(1)->nullable();
            
            $table->string('drop_off_street_address')->nullable();
            $table->string('drop_off_unit')->nullable();
            $table->string('drop_off_state')->nullable();
            $table->string('drop_off_city')->nullable();
            $table->string('drop_off_zipcode')->nullable();
            $table->string('drop_off_contact_number')->nullable();
            $table->text('drop_off_instructions')->nullable();
            $table->text('drop_off_date')->nullable();
            $table->tinyInteger('drop_off_at_time')->default(1)->nullable();

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
        Schema::dropIfExists('pickup_dropoff_address');
    }
};
