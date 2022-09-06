<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\adminpanel\Patients;

class Patient_Tests extends Model
{
    use HasFactory;
    protected $table='patient_tests';
    protected $primaryKey='id';
    protected $fillable = [
        'opdno',
        'prescription_date',
        'advised_tests',
        'patient_id',
        'user_id'
    ];

        /*associated with the Patient_Tests
         *
         * @return \Illuminate\Database\Eloquent\Relations\HasOne
         */
        public function patient()
        {
            return $this->hasMany(Patients::class, 'id','patient_id');
        }
       
    
}
