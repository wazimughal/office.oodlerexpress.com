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
           return $this->hasMany(comments::class, 'quote_id','id')->with('user')->where('comment_section','delivery');
       }
    public function lead_comments()
       {
           return $this->hasMany(comments::class, 'quote_id','id')->with('user')->where('comment_section','lead');
       }
    public function delivery_notes()
       {
           return $this->hasMany(comments::class, 'quote_id','id')->with('user')->where('comment_section','delivery_notes_only');
       }
    public function quote_prices()
       {
           return $this->hasMany(quote_prices::class,'quote_id','id');
       }
    public function quote_agreed_cost()
       {
           return $this->hasOne(quote_prices::class,'quote_id','id')->where('status',1);
       }
    public function invoices()
       {
           return $this->hasMany(invoices::class,'quote_id','id');
       }
    public function delivery_proof()
       {
           return $this->hasMany(files::class,'quote_id','id')->where('slug','proof_of_delivery');
       }
    public function delivery_documents_for_driver()
       {
           return $this->hasMany(files::class,'quote_id','id')->where('slug','document_for_driver');
       }
    public function delivery_documents_for_admin()
       {
           return $this->hasMany(files::class,'quote_id','id')->where('slug','document_for_admin');
       }
    public function document_for_delivery()
       {
           return $this->hasMany(files::class,'quote_id','id')->where('slug','document_for_delivery');
       }
    public function document_for_request_quote()
       {
           return $this->hasMany(files::class,'quote_id','id')->where('slug','quote_request_file');
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
