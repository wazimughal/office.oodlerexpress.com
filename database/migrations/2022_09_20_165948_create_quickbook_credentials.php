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
           Schema::create('quickbook_credentials', function (Blueprint $table) {
            $table->id();
            $table->text('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->text('redirect_uri')->nullable();
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->text('realm_id')->nullable();
            $table->text('base_url')->nullable();
            $table->text('api_url')->nullable();
            $table->text('others')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->text('updating_time')->nullable();
            $table->timestamps();
        });
         // Insert some stuff
         DB::table('quickbook_credentials')->insert(
            array(
                [
                'client_id' => env('QUICKBOOKS_CLIENT_ID'),
                'client_secret' => env('QUICKBOOKS_CLIENT_SECRET'),
                'redirect_uri' => env('QUICKBOOKS_REDIRECT_URI'),
                'access_token' => env('QUICKBOOKS_ACCESS_TOKEN'),
                'refresh_token' => env('QUICKBOOKS_REFRESH_TOKEN'),
                'realm_id' => env('QUICKBOOKS_REALM_ID'),
                'base_url' => env('QUICKBOOKS_BASE_URL'),
                'api_url' => env('QUICKBOOKS_API_URL'),
                
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
        Schema::dropIfExists('quickbook_credentials');
    }
};
