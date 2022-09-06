<?php

namespace App\Models\adminpanel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientReports extends Model
{
    use HasFactory;
    protected $table='patient_reports';
    protected $primaryKey='id';

    public function LabTest()
    {
        return $this->belongsTo(LabTests::class, 'lab_test_id','id');
    }
}

