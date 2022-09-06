<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue_groups extends Model
{
    use HasFactory;
    protected $table='venue_groups';
    protected $primaryKey='id';
    protected $fillable = [
        'id',
        'name',
    ];

    public function ownerinfo()
    {
        return $this->belongsTo(Users::class, 'user_id','id');
    }
    // It works 
    public function ownerinfo2()
    {
        return $this->belongsTo(Users::class, 'user_id','id')->where('status','=',1);
    }
}
