<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class quickbook_credentials extends Model
{
    use HasFactory;
    protected $table='quickbook_credentials';
    protected $primaryKey='id';
}
