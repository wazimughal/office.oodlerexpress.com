<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class activitiestLog extends Model
{
    use HasFactory;
    protected $table='activities_log';
    protected $primaryKey='id';

    public function userData()
       {
           return $this->hasOne(users::class, 'id', 'user_id');
       }
}
