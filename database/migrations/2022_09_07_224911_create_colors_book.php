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
        Schema::create('colors_book', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('bg_color')->nullable();
            $table->string('color_value')->nullable();
            $table->string('color_for')->nullable();
            $table->string('description')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
        // Insert some stuff
        DB::table('colors_book')->insert(
            array(
                [
                'title' => 'Complete Orders',
                'slug' => phpslug('Chaudhary'),
                'bg_color' => '#00a65a',
                'color_value' => '#ffffff',
                'color_for' => 'success',
                'user_id' => 1,
                ],
                [
                'title' => 'Pending Orders',
                'slug' => phpslug('Pending Orders'),
                'bg_color' => '#f56954',
                'color_value' => '#ffffff',
                'color_for' => 'danger',
                'user_id' => 1,
                ],
                [
                'title' => 'Awaited Orders',
                'slug' => phpslug('Awaited Orders'),
                'bg_color' => '#f39c12',
                'color_value' => '#ffffff',
                'color_for' => 'awaited',
                'user_id' => 1,
                ],
                [
                'title' => 'New Order',
                'slug' => phpslug('New Order'),
                'bg_color' => '#3c8dbc',
                'color_value' => '#ffffff',
                'color_for' => 'info',
                'user_id' => 1,
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
        Schema::dropIfExists('colors_book');
    }
};
