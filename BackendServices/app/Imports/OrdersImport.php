<?php

namespace App\Imports;

use App\Order;
use Maatwebsite\Excel\Concerns\ToModel;

class OrdersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        $cbm = number_format((floatval($row[11]) * floatval($row[10]) * floatval($row[9]) * floatval($row[6])), 2);
        $data = [
            //
            'tracking_no'     => $row[0],
            'china_arrival_date'    => $row[1],
            'cargo'    => $row[5], 
            'package_amount'    => $row[6],
            'weight_kg'    => $row[8],
            'longs'    => $row[9],
            'widths'    => $row[10],
            'heights'    => $row[11],
            'cbm'    => $cbm/*$row[12]*/,
            'china_departure_date'    => $row[13],
            'bill_no'    => $row[14]
        ];

        if(!empty($data['tracking_no'])){
            // find by tracking_no
            $order = Order::where('tracking_no', $data['tracking_no'])->first();
            if($order){
                $order->update($data);
            }
        }
        
    }
}
