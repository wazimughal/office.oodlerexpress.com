<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quotes extends Model
{
    use HasFactory;
    protected $table='quotes';
    protected $primaryKey='id';
    public function quote_products()
       {
           return $this->hasMany(quote_products::class, 'quote_id', 'id')->with('category')->with('pickup_dropoff_address');
       }
    public function customer()
       {
           return $this->hasOne(users::class, 'id', 'customer_id');
       }
    public function driver()
       {
           return $this->hasOne(users::class, 'id', 'driver_id');
       }
    public function comments()
       {
           return $this->hasMany(comments::class, 'quote_id','id')->with('user');
       }
    public function quote_prices()
       {
           return $this->hasMany(quote_prices::class,'quote_id','id');
       }
    public function delivery_proof()
       {
           return $this->hasMany(files::class,'quote_id','id')->where('slug','proof_of_delivery');
       }
    public function files()
       {
           return $this->hasMany(files::class,'quote_id','id');
       }
    public function greaterthanPending()
       {
           return $this->status >= config('constants.quote_status.pending');
       }

       //return $this->hasOne(User::class, 'foreign_key', 'local_key');
}
