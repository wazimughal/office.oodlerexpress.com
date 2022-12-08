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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('quote_id')->nullable(); // Quote at which comment is being done
            $table->unsignedBigInteger('lead_id')->nullable(); // Lead at which comment is being done
            $table->foreign('quote_id')->references('id')->on('quotes');
            $table->unsignedBigInteger('user_id'); // Commented by
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')->references('id')->on('groups');
            $table->string('slug')->nullable();
            $table->string('comment_section')->nullable();// Comment for Lead/Quote/Booking...
            $table->tinyInteger('status')->default(1);// Comment can be hidden with status 0 or delete status:3
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
