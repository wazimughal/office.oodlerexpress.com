<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTests extends Model
{
    use HasFactory;
    protected $table='lab_tests';
    protected $primaryKey='id';
    protected $fillable = [
        'test_name',
        'test_slug',
        'description',
        'is_activ',
        'organization_id',
        'user_id'
    ];
   
       /**
        * Get all of the comments for the LabTests
        *
        * @return \Illuminate\Database\Eloquent\Relations\HasMany
        */
       public function getParams()
       {
           return $this->hasMany(LabTestsParams::class, 'lab_test_id', 'id');
       }
       
   
}
