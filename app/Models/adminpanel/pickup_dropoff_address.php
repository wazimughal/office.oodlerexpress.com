<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pickup_dropoff_address extends Model
{
    use HasFactory;
    protected $table='pickup_dropoff_address';
    protected $primaryKey='id';
}
