<?php

namespace App\Exports;

use App\Models\adminpanel\Quotes;
use App\Models\adminpanel\Users;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromArray;

class ExportDriverDeliveries implements FromArray, WithHeadings
{
     /**
    * @return \Illuminate\Support\Collection
    */
    function __construct($data=array()) {
        
        $this->data=$data;
        
      }
    public function array(): array
    {
        return $this->data;
        return $dataArray;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //
    }
    public function headings(): array
    {
                return ['ID',
                        'PO Number',
                        // 'Quote Type',
                        // 'Delivery Type',
                        // 'Pick-up Address',
                        // 'Pickup Unit',
                        // 'Pickup State',
                        // 'Pickup City',
                        // 'Pickup Zipcode',
                        // 'Pickup Contact Number',
                        // 'Pickup Email',
                        // 'Pickup date',
                        // 'Drop-off Address',
                        // 'Drop-off Unit',
                        // 'Drop-off City',
                        // 'Drop-off Zipcode',
                        // 'Drop-off Contact Number',
                        // 'Drop-off Email',
                        'Drop-off Date',
                        // 'Drop-off Instructions',
                        // 'Status',
                        // 'Customer Name',
                        // 'Customer Email',
                        // 'Customer Mobile No.',
                        // 'Customer Business Name',
                        'Driver Name',
                        // 'Driver Email',
                        // 'Driver Mobile No',
                        // 'Driver License No',
                        'Working hours',
    ];
       // return ["ID", "PO Number", "Email"];
    }
}
