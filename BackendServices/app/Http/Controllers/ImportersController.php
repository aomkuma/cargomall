<?php

namespace App\Http\Controllers;

use App\Importer;

use Excel;

use App\Imports\ImportersImport;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

class ImportersController extends Controller
{

    public function pendingList(){

        $list = Importer::with('customer')
                ->whereIn('importer_status', [1,5])
                ->orderBy('created_at', 'DESC')
                ->get();

        $this->data_result['DATA'] = $list;
        
        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function list(Request $request){

        $params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $condition = $params['obj']['condition'];
        $currentPage = $params['obj']['currentPage'];
        $limitRowPerPage = $params['obj']['limitRowPerPage'];

        $currentPage = $currentPage - 1;

        $limit = $limitRowPerPage;
        $offset = $currentPage;
        $skip = $offset * $limit;

        $totalRows = Importer::
                    join('user', 'user.id', '=', 'importer.user_id')
                    ->where(function($query) use ($condition){

                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('tracking_no', $condition['keyword']);
                            $query->orWhere('user_code', $condition['keyword']);
                        }

                        if(isset($condition['importer_status']) &&  !empty($condition['importer_status'])){
                            $query->where('importer_status', $condition['importer_status']);
                        }
                    })
                    ->count();

        $list = Importer::select('importer.*', 'user.user_code')
                    // with('customer')
                    ->with(array('customer'=>function($query){
                            $query->with('addresses');
                        }
                    ))
                    ->join('user', 'user.id', '=', 'importer.user_id')
                    ->where(function($query) use ($condition){

                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('tracking_no', $condition['keyword']);
                            $query->orWhere('user_code', $condition['keyword']);
                        }

                        if(isset($condition['importer_status']) &&  !empty($condition['importer_status'])){
                            $query->where('importer_status', $condition['importer_status']);
                        }

                    })
                    ->orderBy('importer.created_at', 'DESC')
                    ->skip($skip)
                    ->take($limit)
                    ->get();

        $this->data_result['DATA']['DataList'] = $list;
        $this->data_result['DATA']['Total'] = $totalRows;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function listByUser(Request $request){

        $params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $condition = $params['obj']['condition'];

        $list = Importer::where('user_id', $user_data['id'])
                ->where(function($query) use ($condition){

                    if(isset($condition['importer_status']) && !empty($condition['importer_status'])){
                        $query->where('importer_status' , $condition['importer_status']);
                    }
                    
                    if(isset($condition['pay_type']) && $condition['pay_type'] == 5){
                        $query->where('importer_status' , 4);    
                    }

                    if(isset($condition['to_ref_id']) &&  !empty($condition['to_ref_id'])){
                        $query->where('id' , $condition['to_ref_id']);
                    }

                    if(isset($condition['tracking_no']) && !empty($condition['tracking_no'])){
                        $query->where('tracking_no' , $condition['tracking_no']);  
                    } 
                })
                ->orderBy('thai_arrival', 'DESC')
                ->orderBy('updated_at', 'DESC')
                ->get();

        $this->data_result['DATA'] = $list;

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function listByUserLimit(Request $request){

        $params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];

        $list = Importer::where('user_id', $user_data['id'])
                ->orderBy('thai_arrival', 'DESC')
                ->orderBy('updated_at', 'DESC')
                ->take(5)
                ->get();

        $this->data_result['DATA'] = $list;

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function get(Request $request){
        
        $params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $importer_id = $params['obj']['importer_id'];

        $data = Importer::with(array('customer'=>function($query){
                            // $query->with('addresses');
                        }
                    ))
                ->with('customerAddress')
                // ->join('user_address', 'user_address.id', '=', 'importer.customer_address_id')
                ->where('id', $importer_id)->first();

        $this->data_result['DATA'] = $data;

        return $this->returnResponse(200, $this->data_result, response(), false);
    }


    public function delete(Request $request){
        
        $params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $importer_id = $params['obj']['id'];

        $result = Importer::find($importer_id)->delete();

        $this->data_result['DATA'] = $result;

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function update(Request $request){
        
        $params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $Data = $params['obj']['Data'];

        if(isset($Data['china_arrival'])){
            $Data['china_arrival'] = getDateTimeFromString($Data['china_arrival']);    
        }
        if(isset($Data['china_departure'])){
            $Data['china_departure'] = getDateTimeFromString($Data['china_departure']);
        }
        if(isset($Data['thai_arrival'])){
            $Data['thai_arrival'] = getDateTimeFromString($Data['thai_arrival']);
        }
        if(isset($Data['thai_departure'])){
            $Data['thai_departure'] = getDateTimeFromString($Data['thai_departure']);
        }
        if(isset($Data['transport_company']) && $Data['transport_company'] != 'other'){
            $Data['transport_company_other'] = null;
        }

        if(!isset($Data['id'])){
            $Data['id'] = generateID();
            
            if(!isset($Data['user_id'])){
                $Data['user_id'] = $user_data['id'];
            }
            
            $Data['importer_status'] = 1;
            $result = Importer::create($Data);
        }else{
            $Data['id'] = trim($Data['id']);
            $result = Importer::find($Data['id'])->update($Data);
        }

        $this->data_result['DATA'] = $result;

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function updateImporterStatus(Request $request){

        $params = $request->all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $importer_id = trim($params['obj']['importer_id']);
        $to_importer_status = $params['obj']['to_importer_status'];

        Log::info("UPDATE_IMPORTER_STATUS => ");
        Log::info("User : " . $user_data['firstname'] . ' ' . $user_data['lastname']);

        $importer = Importer::find($importer_id);
        if($importer){

            $current_importer_status = $importer->to_importer_status;

            $importer->updated_at = date('Y-m-d H:i:s');
            $importer->importer_status = $to_importer_status;
            $importer->save();

            if($to_importer_status == 4 && $current_importer_status < $to_importer_status){
                $mobile_no = $importer->customer->mobile_no;
                $message = 'รายการนำเข้าสินค้าเลขที่ ' . $importer->tracking_no . ' ขณะนี้อยู่ในสถานะ "รอการชำระค่าขนส่ง" กรุณาเข้าสู่ระบบเพื่อดำเนินการชำระค่าบริการ ขอบคุณค่ะ';
                sendSms($mobile_no, $message);
            }

            Log::info("Tracking No : " . $importer->tracking_no);
            Log::info("To Importer Status : " . $to_importer_status);

            $this->data_result['DATA'] = true;
        }else{

            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'Order not found';

        }

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function uploadExcel(Request $request){

        $file = $request->file();
        $AttachFile = $file['obj']['AttachFile'];

        $excel_path = $AttachFile->storeAs(
                    'importer_excel', 'importer__excel_'. date('YmdHis')  . '.' . $AttachFile->getClientOriginalExtension()
                );

        // echo "Begin ";
        \Log::info("Begin call command..");
        exec('C:\xampp\php\php C:\xampp\htdocs\cargomall\BackendServices\artisan command:read_importer_excel ' . $excel_path);
        \Log::info("End call command..");
        // echo "Result ";
        // var_dump($arr);
        // exit;

        $this->data_result['STATUS'] = 'ERROR';
        $this->data_result['DATA'] = 'ระบบกำลังทำการอ่านข้อมูลไฟล์สินค้า กรุณาตรวจสอบข้อมูลอีกครั้งในอีก 5 นาที';

        \Log::info("Return Result");

        return $this->returnResponse(200, $this->data_result, response(), false);

    }
}
