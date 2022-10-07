<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class comments extends Model
{
    use HasFactory;
    protected $table='comments';
    protected $primaryKey='id';
    public function user()
    {
        return $this->hasOne(users::class, 'id', 'user_id');
    }
     //return $this->hasOne(User::class, 'foreign_key', 'local_key');
}
