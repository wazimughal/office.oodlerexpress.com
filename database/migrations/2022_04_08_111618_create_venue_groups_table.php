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
        Schema::create('venue_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('lang',50)->nullable();
            $table->string('lat',50)->nullable();
            $table->text('address');
            $table->string('hod_name',100)->nullable();
            $table->string('hod_designation')->nullable();
            $table->text('description')->nullable();
            $table->string('hod_phone')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });


        // Insert some stuff
        DB::table('venue_groups')->insert(
            array(
                [
                'name' => 'Ateres Charna',
                'lang' => '30.0358172',
                'lat' => '72.3670309',
                'address' => 'New Sharqi Colony DHQ Hospital Vehari',
                'hod_name' => 'Chaudhary Wasim',
                'hod_designation' => 'Genral Manager',
                'description' => 'Serve with Pride',
                'hod_phone' => '03007543712',
                'user_id' => 2,
                'is_active' => 1,
                ],
                [
                    'name' => 'Ateres Frieda',
                    'lang' => '30.0358172',
                    'lat' => '72.3670309',
                    'address' => 'New Sharqi Colony DHQ Hospital Vehari',
                    'hod_name' => 'Chaudhary Wasim',
                    'hod_designation' => 'Genral Manager',
                    'description' => 'Serve with Pride',
                    'hod_phone' => '0300743212',
                    'user_id' => 3,
                    'is_active' => 1,
                ],
                [
                    'name' => 'Avir Yaakov',
                    'lang' => '30.0358172',
                    'lat' => '72.3670309',
                    'address' => 'New Sharqi Colony DHQ Hospital Vehari',
                    'hod_name' => 'Chaudhary Wasim',
                    'hod_designation' => 'Genral Manager',
                    'description' => 'Serve with Pride',
                    'hod_phone' => '030012312',
                    'user_id' => 4,
                    'is_active' => 1,
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
        Schema::dropIfExists('organizations');
    }
};
