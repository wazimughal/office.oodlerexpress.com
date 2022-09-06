<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patients extends Model
{
    use HasFactory;
    protected $table='patients';
    protected $primaryKey='id';
    protected $fillable = [
        'name',
        'phone',
        'gender',
        'is_active',
        'group_id',
        'user_id'
    ];

    public function getAdvisedTests()
       {
           return $this->hasMany(Patient_Tests::class, 'patient_id', 'id');
       }
}
