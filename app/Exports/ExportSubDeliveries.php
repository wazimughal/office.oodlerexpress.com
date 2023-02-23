<?php

namespace App\Exports;

use App\Models\adminpanel\Quotes;
use App\Models\adminpanel\Users;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromArray;

class ExportSubDeliveries implements FromArray, WithHeadings
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
                return ['No.',
                        'PO Number',
                        'Pick-up Address',
                        'Pick-up Date',
                        'Drop-off Address',
                        'Drop-off Date',
                        'Sub Business Name',
                        'Delivery Cost',
                        'Sub earning',
                    ];
    }
}
