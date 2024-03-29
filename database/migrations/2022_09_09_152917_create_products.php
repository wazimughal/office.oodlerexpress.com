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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('image')->nullable();
            $table->text('sizes')->nullable();
            $table->string('size')->nullable();
            $table->string('size_unit')->nullable();
            $table->string('weight')->nullable();
            $table->string('additional_notes')->nullable();
            $table->string('is_active')->default(1);
            $table->unsignedBigInteger('cat_id');
            $table->foreign('cat_id')->references('id')->on('product_categories');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->tinyInteger('added_by')->default(1)->nullable(); //1 is for admin, 2 is for other user
            $table->timestamps();
        });
         // Insert some stuff
         DB::table('products')->insert(
            array(
                [
                'name' => 'welded frames',
                'slug' => phpslug('welded frames'),
                'is_active' => 1,
                'sizes' => 'size 1, size 2, size 3',
                'cat_id' => 2,
                'user_id' => 1,
                'added_by' => 1,
                ],
                [
                'name' => 'Wood Doors',
                'slug' => phpslug('wood doors'),
                'is_active' => 1,
                'sizes' => 'size 1, size 2, size 3',
                'cat_id' => 2,
                'user_id' => 1,
                'added_by' => 1,
                ],
                [
                'name' => 'Filters',
                'slug' => phpslug('Filters'),
                'sizes' => 'size 1, size 2, size 3',
                'is_active' => 1,
                'cat_id' => 1,
                'user_id' => 1,
                'added_by' => 1,
                ],
                [
                'name' => 'Panels',
                'slug' => phpslug('Panels'),
                'sizes' => 'size 1, size 2, size 3',
                'is_active' => 1,
                'cat_id' => 1,
                'user_id' => 1,
                'added_by' => 1,
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
        Schema::dropIfExists('products');
    }
};
