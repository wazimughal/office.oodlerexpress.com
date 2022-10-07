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
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name',50);
            $table->string('slug',50)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });
            // Insert some stuff
            DB::table('states')->insert(
                array(
                    
                    [
                    'name' => 'Alabama',
                    'is_active' => '1',
                    'slug' => phpslug('Alabama'),
                    ],
                    [
                    'name' => 'Alaska',
                    'is_active' => '1',
                    'slug' => phpslug('Alaska'),
                    ],
                    [
                    'name' => 'Arizona',
                    'is_active' => '1',
                    'slug' => phpslug('Arizona'),
                    ],
                    [
                    'name' => 'California',
                    'is_active' => '1',
                    'slug' => phpslug('California'),
                    ],
                    [
                    'name' => 'Washington',
                    'is_active' => '1',
                    'slug' => phpslug('Washington'),
                    ],
                    [
                    'name' => 'South Carolina',
                    'is_active' => '1',
                    'slug' => phpslug('South Carolina'),
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
        Schema::dropIfExists('states');
    }
};
