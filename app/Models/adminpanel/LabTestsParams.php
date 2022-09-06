<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabTestsParams extends Model
{
    use HasFactory;
    protected $table='lab_tests_params';
    protected $primaryKey='id';
    protected $fillable = [
        'parameter_name',
        'parameter_result',
        'parameter_unit',
        'parameter_normal_range',
        'comments',
        'lab_test_id'
    ];

    // public function ParamResult()
    // {
    //     return $this->hasOne(PatientReports::class, 'lab_tests_params_id','id');
    // }
    public function LabTest()
    {
      
        return $this->belongsTo(LabTests::class, 'lab_test_id','id');
    }

   
}
