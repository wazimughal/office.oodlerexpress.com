<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    use HasFactory;
    protected $table='users';
    protected $primaryKey='id';


    function __construct() {
     
        
    }
    public function getGroups()
       {
           return $this->hasOne(Groups::class, 'id', 'group_id');
       }
     public function City()
       {
           return $this->hasOne(cities::class, 'id', 'city_id');
       }
    public function ZipCode()
       {
           return $this->hasOne(zipcode::class, 'id', 'zipcode_id');
       }
    public function files()
       {
           return $this->hasMany(FilesManage::class, 'user_id', 'id');
       }
}
