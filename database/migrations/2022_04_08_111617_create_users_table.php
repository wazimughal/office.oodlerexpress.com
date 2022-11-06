<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('cnic')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('subject')->nullable();
            $table->string('message')->nullable();
            $table->string('mobileno')->nullable();
            $table->string('business_name')->nullable();
            $table->string('business_email')->nullable();
            $table->string('business_address')->nullable();
            $table->string('business_mobile')->nullable();
            $table->string('business_phone')->nullable();
            $table->string('years_in_business')->nullable();
            $table->string('designation')->nullable();
            $table->string('shipping')->nullable();
            $table->text('shipping_cat')->nullable();
            $table->string('how_often_shipping')->nullable()->default('monthly');
            $table->string('license_no')->nullable();
            $table->string('country')->default('USA');
            $table->string('street')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('password')->nullable();
            $table->tinyInteger('lead_by')->default(0); //0: By Office , 1: by Website
            $table->string('profile_pic')->nullable();
            $table->tinyInteger('is_active')->default(0 );
            $table->tinyInteger('status')->default(0 );
            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')->references('id')->on('groups');
            
            $table->rememberToken();
            $table->timestamps();
        });

         // Insert some stuff
         DB::table('users')->insert(
            array(
                [
                'name' => 'Chaudhary Wasim',
                'firstname' => 'Chaudhary ',
                'lastname' => 'Wasim',
                'email' => 'admin@gmail.com',
                'cnic' => '3660327946615',
                'phone' => '03007731712',
                'password' => Hash::make('1234'),
                'shipping_cat' => json_encode(array(1,2,3)),
                'is_active' => 1,
                'group_id' => 1,
                ]
                
                
            )
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
