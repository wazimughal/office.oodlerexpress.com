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
                    'name' => 'Punjab',
                    'is_active' => '1',
                    'slug' => phpslug('punjab'),
                    ],
                    [
                    'name' => 'Khyber Pakhtunkhawa',
                    'is_active' => '1',
                    'slug' => phpslug('Khyber Pakhtunkhawa'),
                    ],
                    [
                    'name' => 'Sindh',
                    'is_active' => '1',
                    'slug' => phpslug('sindh'),
                    ],
                    [
                    'name' => 'Balochistan',
                    'is_active' => '1',
                    'slug' => phpslug('Balochistan'),
                    ],
                    [
                    'name' => 'Azad Kashmir',
                    'is_active' => '1',
                    'slug' => phpslug('Azad Kashmir'),
                    ],
                    [
                    'name' => 'Islamabad(Capital)',
                    'is_active' => '1',
                    'slug' => phpslug('Islamabad'),
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
