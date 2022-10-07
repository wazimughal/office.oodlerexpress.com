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
                'name' => 'Monsey',
                'slug' => phpslug('Monsey'),
                'is_active' => '1'
                ],
                [
                'name' => 'Monroe',
                'slug' => phpslug('Monroe'),
                'is_active' => '1'
                ],
                [
                'name' => 'Brooklyn',
                'slug' => phpslug('Brooklyn'),
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
