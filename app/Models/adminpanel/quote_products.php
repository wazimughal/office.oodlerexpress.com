<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class quote_products extends Model
{
    use HasFactory;
    protected $table='quote_products';
    protected $primaryKey='id';

    public function quote()
    {
        return $this->hasOne(quotes::class, 'id', 'quote_id');
    }
    public function category()
    {
        return $this->hasOne(product_categories::class, 'id', 'cat_id');
    }
    //return $this->hasOne(User::class, 'foreign_key', 'local_key');
}
