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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name',50);
            $table->string('slug',50)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

        });
         // Insert some stuff
         DB::table('cities')->insert(
            array(
                
                [
                'name' => 'Multan',
                'slug' => phpslug('Multan'),
                'is_active' => '1'
                ],
                [
                'name' => 'Vehari',
                'slug' => phpslug('Vehari'),
                'is_active' => '1'
                ],
                [
                'name' => 'Okara',
                'slug' => phpslug('Okara'),
                'is_active' => '1'
                ],
                [
                'name' => 'Sahiwal',
                'slug' => phpslug('Sahiwal'),
                'is_active' => '1'
                ],
                [
                'name' => 'khanewal',
                'slug' => phpslug('Khanewal'),
                'is_active' => '1'
                ],
                [
                'name' => 'MuzafarGhar',
                'slug' => phpslug('MuzafarGhar'),
                'is_active' => '1'
                ],
                [
                'name' => 'Lodharan',
                'slug' => phpslug('Lodharan'),
                'is_active' => '1'
                ],
                [
                'name' => 'Khushab',
                'slug' => phpslug('Khushab'),
                'is_active' => '1'
                ],
                [
                'name' => 'MianWali',
                'slug' => phpslug('MianWali'),
                'is_active' => '1'
                ],
                [
                'name' => 'Rajan Pur',
                'slug' => phpslug('Rajan Pur'),
                'is_active' => '1'
                ],
                [
                'name' => 'DG Khan',
                'slug' => phpslug('DG Khan'),
                'is_active' => '1'
                ],
                [
                'name' => 'PakPattan',
                'slug' => phpslug('PakPattan'),
                'is_active' => '1'
                ],
                [
                'name' => 'Kasoor',
                'slug' => phpslug('Kasoor'),
                'is_active' => '1'
                ],
                [
                'name' => 'Sheikhupura',
                'slug' => phpslug('Sheikhupura'),
                'is_active' => '1'
                ],
                [
                'name' => 'Chakwal',
                'slug' => phpslug('Chakwal'),
                'is_active' => '1'
                ],
                [
                'name' => 'Lahore',
                'slug' => phpslug('Lahore'),
                'is_active' => '1'
                ],
                [
                'name' => 'Rawalpindi',
                'slug' => phpslug('RawalPindi'),
                'is_active' => '1'
                ],
                [
                'name' => 'Islambad',
                'slug' => phpslug('Islambad'),
                'is_active' => '1'
                
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
        Schema::dropIfExists('districts');
    }
};
