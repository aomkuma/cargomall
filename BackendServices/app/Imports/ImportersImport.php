<?php

namespace App\Imports;

use App\Importer;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $china_arrival = empty($row[1])?null:str_replace('.', '-', $row[1]);
        $china_departure = empty($row[13])?null:str_replace('.', '-', $row[13]);
        $thai_arrival = empty($row[16])?null:str_replace('.', '-', $row[16]);

        $cbm = number_format((floatval($row[11]) * floatval($row[10]) * floatval($row[9]) * floatval($row[6])), 3);

        $data = [
            //
            'tracking_no'     => trim($row[0]),
            'china_arrival'    => $china_arrival,
            'cargo'    => $row[5], 
            'package_amount'    => $row[6],
            'weight_kgm'    => $row[8],
            'longs'    => $row[9],
            'width'    => $row[10],
            'height'    => $row[11],
            'cbm'    => $cbm/*$row[12]*/,
            'china_departure'    => $china_departure,
            'bill_no'    => $row[14],
            'container_no'    => $row[15],
            'thai_arrival'    => $thai_arrival
        ];

        if(!empty($data['tracking_no'])){
            // find by tracking_no
            $importer = Importer::where('tracking_no', $data['tracking_no'])->first();
            if($importer){
                if($importer->importer_status == 1){
                    $data['importer_status'] = 2;
                }
                $importer->update($data);
                // Importer::find($importer->id)->update($data);
            }
        }
    }
}
