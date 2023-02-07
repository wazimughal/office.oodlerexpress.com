<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product_categories extends Model
{
    use HasFactory;
    protected $table='product_categories';
    protected $primaryKey='id';

    /**
         * Get all of the comments for the product_categories
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function products()
        {
            return $this->hasMany(products::class, 'cat_id', 'id')->where('is_active',1);
        }
    
}
