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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_type');
            $table->string('business_type');
            $table->tinyInteger('elevator')->nullable()->default(0);
            $table->string('no_of_appartments')->nullable();
            $table->text('list_of_floors')->nullable();
            $table->string('po_number');
            $table->string('pickup_street_address')->nullable();
            $table->string('pickup_unit')->nullable();
            $table->unsignedBigInteger('pickup_state_id')->default(1);
            $table->foreign('pickup_state_id')->references('id')->on('states');
            $table->unsignedBigInteger('pickup_city_id')->default(1);
            $table->foreign('pickup_city_id')->references('id')->on('cities');
            $table->unsignedBigInteger('pickup_zipcode_id')->default(1);
            $table->foreign('pickup_zipcode_id')->references('id')->on('zipcode');
            $table->string('pickup_contact_number')->nullable();
            $table->text('pickup_date')->nullable();
            $table->tinyInteger('pickup_at_time')->default(1);
            
            $table->string('drop_off_street_address')->nullable();
            $table->string('drop_off_unit')->nullable();
            $table->unsignedBigInteger('drop_off_state_id')->default(1);
            $table->foreign('drop_off_state_id')->references('id')->on('states');
            $table->unsignedBigInteger('drop_off_city_id')->default(1);
            $table->foreign('drop_off_city_id')->references('id')->on('cities');
            $table->unsignedBigInteger('drop_off_zipcode_id')->default(1);
            $table->foreign('drop_off_zipcode_id')->references('id')->on('zipcode');
            $table->string('drop_off_contact_number')->nullable();
            $table->text('drop_off_instructions')->nullable();
            $table->text('drop_off_date')->nullable();
            $table->tinyInteger('drop_off_at_time')->default(1);

            $table->tinyInteger('status')->default(0);
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('users');

            $table->unsignedBigInteger('driver_id')->nullable();
            $table->foreign('driver_id')->references('id')->on('users');

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
        Schema::dropIfExists('quotes');
    }
};
