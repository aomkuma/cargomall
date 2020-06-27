<?php

namespace App\Imports;

use App\Order;
use App\OrderTracking;
use Maatwebsite\Excel\Concerns\ToModel;

use DB;

class OrdersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $row[12] = floatval(trim($row[12]));
        if(empty($row[12])){
            $cbm = number_format((floatval($row[11]) * floatval($row[10]) * floatval($row[9]) * floatval($row[6])), 2);
        }else{
            $cbm = $row[12];
        }

        $goods_desc_th = null;

        if(!empty($row[4])){
            $goods_desc_th_arr = explode('"', trim($row[4]));
            $goods_desc_th = $goods_desc_th_arr[count($goods_desc_th_arr) - 2];
        }

        $data = [
            //
            'tracking_no'     => trim($row[0]),
            'china_arrival_date'    => trim($row[1]),
            'goods_desc_en' => trim($row[3]),
            'goods_desc_th' => $goods_desc_th,
            'cargo'    => trim($row[5]), 
            'package_amount'    => trim($row[6]),
            'weight_kg'    => trim($row[8]),
            'longs'    => trim($row[9]),
            'widths'    => trim($row[10]),
            'heights'    => trim($row[11]),
            'cbm'    => $cbm/*$row[12]*/,
            'china_departure_date'    => trim($row[13]),
            'bill_no'    => trim($row[14]),
            'container_no'    => $row[15],
            'thai_arrival'    => $row[16]
        ];

        $data['tracking_no'] = trim($data['tracking_no']);

        if(!empty($data['tracking_no'])){
            // find by tracking_no
            $order = Order::where('tracking_no', 'LIKE', DB::raw("'%" . $data['tracking_no'] . "%'"))->first();

            if($order){
                if($order->order_status == 3){
                    $order->order_status = 4;
                    $order->save();
                }
                // find order tracking
                $order_tracking = OrderTracking::where('tracking_no', $data['tracking_no'])->first();

                if($order_tracking){

                    $order_tracking->update($data);

                }else{

                    $data['id'] = generateID();
                    $data['order_id'] = trim($order->id);
                    OrderTracking::create($data);

                }
                
            }
        }
        
    }
}
