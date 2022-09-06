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
            $table->string('mobileno')->nullable()->unique();
            $table->string('business_name')->nullable();
            $table->string('business_address')->nullable();
            $table->string('business_mobile')->nullable();
            $table->string('business_phone')->nullable();
            $table->string('license_no')->nullable();
            $table->string('country')->default('USA');
            $table->unsignedBigInteger('state_id')->nullable();
            $table->foreign('state_id')->references('id')->on('states');
            $table->unsignedBigInteger('city_id')->nullable();
            $table->foreign('city_id')->references('id')->on('cities');
            $table->unsignedBigInteger('zipcode_id')->nullable();
            $table->foreign('zipcode_id')->references('id')->on('zipcode');
            $table->string('password')->nullable();
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
                'zipcode_id' => 1,
                'city_id' => 1,
                'is_active' => 1,
                'group_id' => 1,
                ],
                [
                'name' => 'Muhammad Arshad',
                'firstname' => 'Muhammad',
                'lastname' => 'Arshad VG',
                'email' => 'hod1@gmail.com',
                'cnic' => '123',
                'phone' => '',
                'password' => Hash::make('1234'),
                'zipcode_id' => 1,
                'city_id' => 1,
                'is_active' => 1,
                'group_id' => 2,
                ],
                [
                'name' => 'Wasim  VG',
                'firstname' => 'Wasim ',
                'lastname' => 'Arshad VG',
                'email' => 'hod2@gmail.com',
                'cnic' => '456',
                'phone' => '',
                'password' => Hash::make('1234'),
                'zipcode_id' => 1,
                'city_id' => 1,
                'is_active' => 1,
                'group_id' => 2,
                ],
                [
                'name' => 'Ali Uffan Chadhary',
                'firstname' => 'Ali Uffan',
                'lastname' => 'Chadhary',
                'email' => 'hod@gmail.com',
                'cnic' => '789',
                'phone' => '',
                'password' => Hash::make('1234'),
                'zipcode_id' => 1,
                'city_id' => 1,
                'is_active' => 1,
                'group_id' => 2,
                ],
                [
                'name' => 'Waqas Ali',
                'firstname' => 'Waqas',
                'lastname' =>'Ali',
                'email' => 'staff@gmail.com',
                'cnic' => '3660327946616',
                'phone' => '03007731713',
                'password' => Hash::make('1234'),
                'zipcode_id' => 1,
                'city_id' => 1,
                'is_active' => 1,
                'group_id' => 3,
                ],
                [
                'name' => 'Haroon ahmad',
                'firstname' => 'Haroon',
                'lastname' => 'ahmad',
                'email' => 'subscriber@gmail.com',
                'cnic' => '3660327946617',
                'phone' => '03007731717',
                'password' => Hash::make('1234'),
                'zipcode_id' => 1,
                'city_id' => 1,
                'is_active' => 1,
                'group_id' => 4,
                ],
                
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
