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
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('is_active')->default(1);
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
         // Insert some stuff
        DB::table('product_categories')->insert(
            array(
                [
                'name' => 'Kitchens',
                'slug' => phpslug('Kitchens'),
                'is_active' => 1,
                'user_id' => 1,
                ],
                [
                'name' => 'Doors',
                'slug' => phpslug('Doors'),
                'is_active' => 1,
                'user_id' => 1,
                ],
                [
                'name' => 'Accessories',
                'slug' => phpslug('Accessories'),
                'is_active' => 1,
                'user_id' => 1,
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
        Schema::dropIfExists('product_category');
    }
};
