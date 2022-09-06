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
       // Relation with Users table and Venue_Users to get Venue Group of all users/customers/leads Detail
    public function getVenueGroup()
       {
           return $this->hasOne(venue_users::class, 'id', 'venue_users_id');
       }
       // Relation with Users table and Venue Group to get Venue Group Detail
    public function VenueGroup()
       {
           return $this->hasOne(venue_groups::class, 'user_id', 'id');
       }
    public function City()
       {
           return $this->hasOne(cities::class, 'id', 'city_id');
       }
    public function ZipCode()
       {
           return $this->hasOne(zipcode::class, 'id', 'zipcode_id');
       }
}
