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
        Schema::create('zipcode', function (Blueprint $table) {
            $table->id();
            $table->string('code',50)->unique();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });
        DB::table('zipcode')->insert(
            array(
                [
                'code' => 61107,
                'is_active' => '1',
                ],
                [
                'code' => 61106,
                'is_active' => '1',
                ],
                [
                'code' => 61105,
                'is_active' => '1',
                ],
                [
                'code' => 61104,
                'is_active' => '1',
                ],
                [
                'code' => 61103,
                'is_active' => '1',
                ],
                [
                'code' => 61102,
                'is_active' => '1',
                ],
                [
                'code' => 61101,
                'is_active' => '1',
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
        Schema::dropIfExists('tehsils');
    }
};
