<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class quote_prices extends Model
{
    use HasFactory;
    protected $table='quote_prices';
    protected $primaryKey='id';
    
}
