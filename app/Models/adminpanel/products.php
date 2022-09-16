<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class products extends Model
{
    protected $table='products';
    protected $primaryKey='id';
    use HasFactory;
    public function category()
    {
        return $this->hasOne(product_categories::class, 'id', 'cat_id');
    }

    
}
