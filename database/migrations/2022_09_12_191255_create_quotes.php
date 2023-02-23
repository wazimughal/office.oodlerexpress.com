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
            $table->string('quote_type')->nullable();
            $table->string('business_type')->nullable();
            $table->tinyInteger('elevator')->nullable()->default(0);
            $table->string('no_of_appartments')->nullable();
            $table->text('list_of_floors')->nullable();
            $table->string('po_number')->nullable();

            
            $table->string('pickup_street_address')->nullable();
            $table->string('pickup_unit')->nullable();
            $table->string('pickup_state')->nullable();
            $table->string('pickup_city')->nullable();
            $table->string('pickup_zipcode')->nullable();
            $table->string('pickup_contact_number')->nullable();
            $table->string('pickup_email')->nullable();
            $table->text('pickup_date')->nullable();
            //$table->tinyInteger('pickup_at_time')->default(1);
            
            $table->string('drop_off_street_address')->nullable();
            $table->string('drop_off_unit')->nullable();
            $table->string('drop_off_state')->nullable();
            $table->string('drop_off_city')->nullable();
            $table->string('drop_off_zipcode')->nullable();
            $table->string('drop_off_contact_number')->nullable();
            $table->string('drop_off_email')->nullable();
            $table->text('drop_off_date')->nullable();
            //$table->tinyInteger('drop_off_at_time')->default(1);

            $table->text('drop_off_instructions')->nullable();
            // Driver Activity
            $table->string('reached_at_pickup')->nullable();
            $table->string('picked_up')->nullable();
            $table->string('on_the_way')->nullable();
            $table->string('reached_at_dropoff')->nullable();
            $table->string('delivered')->nullable();
            $table->string('arrived_at_pickup')->nullable();
            $table->string('arriving_at_dropoff')->nullable();

            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('quote_by')->default(0); //0: By Office , 1: by Website
            $table->tinyInteger('is_active')->default(1); //0: In Active , 1: Active, 2: Trash, 3:Delete
            $table->unsignedBigInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('users');
            $table->unsignedBigInteger('request_file_id')->default(0)->nullable();

            $table->tinyInteger('assign_to')->default(1); //1: Driver , 2: Sub
            $table->tinyInteger('sub_status')->default(0); //0: Pending to assign any Sub , 1: Accepted, 2:Rejected

            $table->unsignedBigInteger('driver_id')->nullable();
            $table->foreign('driver_id')->references('id')->on('users');
            
            $table->unsignedBigInteger('sub_id')->nullable();
            $table->foreign('sub_id')->references('id')->on('users');
            $table->string('quoted_price_for_sub')->nullable();
            // All these are quickbooks information
            $table->BigInteger('qb_service_id')->nullable();
            $table->BigInteger('qb_invoice_id')->nullable();
            $table->string('qb_invoice_no')->nullable();
            $table->tinyInteger('payment_status')->default(0)->nullable();

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
