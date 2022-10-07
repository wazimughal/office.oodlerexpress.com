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
                'code' => 10952,
                'is_active' => '1',
                ],
                [
                'code' => 10953,
                'is_active' => '1',
                ],
                [
                'code' => 10954,
                'is_active' => '1',
                ],
                [
                'code' => 71217,
                'is_active' => '1',
                ],
                [
                'code' => 71201,
                'is_active' => '1',
                ],
                [
                'code' => 71202,
                'is_active' => '1',
                ],
                [
                'code' => 44119,
                'is_active' => '1',
                ],
                [
                'code' => 44120,
                'is_active' => '1',
                ],
                [
                'code' => 44121,
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
