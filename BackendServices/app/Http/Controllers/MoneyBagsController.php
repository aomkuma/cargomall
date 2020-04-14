<?php

namespace App\Http\Controllers;

use App\MoneyTopup;
use App\MoneyBag;
use App\MoneyUse;
use App\Order;
use App\OrderDesc;
use App\Importer;
use App\User;
use App\WithdrawnHistory;
use App\RefundHistory;

use App\Mail\OrderRequestMailable;
use App\Mail\OrderTransportCostMailable;
use App\Mail\ImporterCostMailable;
use App\Mail\TopupNotiMailable;
use App\Mail\TransferNotiMailable;
use App\Mail\DepositNotiMailable;

use Mail;

use DB;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

class MoneyBagsController extends Controller
{
    //
    //

    public function topupPendingList(){

        $list = MoneyTopup::with('customer')
                ->where('topup_status', 1)
                ->orderBy('created_at', 'ASC')
                ->get();

        $this->data_result['DATA'] = $list;
        
        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function transferPendingList(){

        $list = MoneyUse::with('customer')
                ->where('pay_type', 3)
                ->where('pay_status', 1)
                ->orderBy('created_at', 'ASC')
                ->get();

        $this->data_result['DATA'] = $list;
        
        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function depositPendingList(){

        $list = MoneyUse::with('customer')
                ->where('pay_type', 4)
                ->where('pay_status', 1)
                ->orderBy('created_at', 'ASC')
                ->get();

        $this->data_result['DATA'] = $list;
        
        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function getAccountBalance(Request $request){
        $params = $request->all();
        $token = $params['user_session']['token'];
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_id = ''.$user_data['id'];

        // $balance = checkAccountBalance($user_id);

        $UserData = User::with(['addresses' => function($q){
                            $q->orderBy('address_no', 'ASC');
                        }])
                        ->with('moneyBags')
                        ->where('id', $user_id)
                        ->first();

        $this->data_result['DATA']['token'] = $token;
        $this->data_result['DATA']['UserData'] = base64_encode($UserData);

        // $this->data_result['DATA'] = $balance;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function topup(Request $request){

        $params = $request->all();
        $file = $request->file();
        $AttachFile = $file['obj']['AttachFile'];

        if(empty($AttachFile->getClientOriginalExtension())){
            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'Invalid request';
            return $this->returnResponse(200, $this->data_result, response(), false);
        }

        $Data = $params['obj']['Data'];
        $Data['user_id'] = ''.$Data['user_id'];
        // upload file
        $slip_path = $AttachFile->storeAs(
            'slip', $Data['user_id'] . '_' . date('YmdHis')  . '.' . $AttachFile->getClientOriginalExtension()
        );
        // echo $slip_path;exit;
  //       print_r($Data);exit;
        $Data['slip_file'] = 'BackendServices/storage/app/' . $slip_path;
        $Data['topup_status'] = 1;
        $Data['id'] = generateID();

        $result = MoneyTopup::create($Data);

        // $this->approveTopup($Data['user_id'], $Data['topup_amount']);

        if($result){
            
            $this->data_result['DATA'] = base64_encode(getUserProfile($Data['user_id']));
            
        }else{
            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'Cannot topup Please try again later.';
        }

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function pay(Request $request){

        $params = $request->all();

        $Data = $params['obj']['Data'];
        $Data['user_id'] = ''.$Data['user_id'];
        $Data['to_ref_id'] = trim($Data['to_ref_id']);

        $SelectedPayBaht = $params['obj']['SelectedPayBaht'];
        $CurrentExchangeRate = $params['obj']['CurrentExchangeRate'];

        if(($Data['pay_type'] == '1' || $Data['pay_type'] == '2' || $Data['pay_type'] == '5')){
            
            if($SelectedPayBaht != $Data['pay_amount_thb']){
                $this->data_result['STATUS'] = 'ERROR';
                $this->data_result['DATA'] = 'จำนวนเงินไม่ตรงกับยอดที่ต้องชำระ กรุณาตรวจสอบความถูกต้อง';
                return $this->returnResponse(200, $this->data_result, response(), false);
            }
            // Check balance
            $money_balance = checkAccountBalance($Data['user_id']);

            if($money_balance < $Data['pay_amount_thb']){

                $this->data_result['STATUS'] = 'ERROR';
                $this->data_result['DATA'] = 'ยอดเงินคงเหลือของคุณไม่พอชำระ กรุณาเติมเงิน';
                return $this->returnResponse(200, $this->data_result, response(), false);

            }
        }

        $Data['id'] = generateID();

        if($Data['pay_type'] == '1' || $Data['pay_type'] == '2' || $Data['pay_type'] == '5'){
            $Data['pay_status'] = 2;
        }else{
            $Data['pay_status'] = 1;
        }

        if($Data['pay_type'] == '3' || $Data['pay_type'] == '4'){
            $Data['exchange_rate'] = null;//getLastChinaRateTransfer();
            $Data['pay_amount_thb'] = null;
            $Data['to_ref_id'] = null;
        }

        if($Data['pay_type'] == '2' || $Data['pay_type'] == '5'){
            $Data['exchange_rate'] = null;
        }
        
        $result = MoneyUse::create($Data);

        if($result){

            if($Data['pay_status'] == 2 && ($Data['pay_type'] == '1' || $Data['pay_type'] == '2' || $Data['pay_type'] == '5')){
                $balance = $this->reCalcMoneyBagBalance($Data['user_id'], $Data['pay_amount_thb']);
            }
            //
            if($Data['pay_type'] == '1'){
                // update order status to 2 (Already pay)
                $order = Order::find($Data['to_ref_id']);
                // print_r($order);
                if($order){
                    
                    $order->order_status = 2;
                    $order->save();

                    // update china_ex_rate
                    $order_desc = OrderDesc::where('order_id', $Data['to_ref_id'])->first();
                    
                    if($order_desc){
                        $order_desc->china_ex_rate = getLastChinaRate();
                        $order_desc->save();
                        
                    }
                    //print_r($order_desc);
                    //exit;
                    // Send mail

                    $order_data = Order::with('orderDesc')
                                ->with('orderDetails')
                                ->with('customer')
                                ->with('customerAddress')
                                ->where('id', $Data['to_ref_id'])
                                ->first();
                    
                    if($order_data->orderDesc->china_ex_rate == 0){
                        $order_data->orderDesc->china_ex_rate = getLastChinaRate();
                    }

                    $email_to = $order_data->customer->email;

                    Mail::to($email_to)->send(new OrderRequestMailable($order_data));

                }
                
            }

            if($Data['pay_type'] == '2'){

                // update order status to 7 (Already pay)
                $order = Order::find($Data['to_ref_id']);

                if($order){
                    $order->order_status = 7;
                    $order->save();

                    $order_data = Order::with('customer')
                                ->where('id', $Data['to_ref_id'])
                                ->first();
                    
                    $Data['id'] = trim($Data['id']);
                    $pay_data = MoneyUse::find($Data['id']);

                    $user_id = trim($order_data->customer->id);
                    $money_bag_data = MoneyBag::where('user_id', $user_id)->first();

                    $email_to = $order_data->customer->email;

                    Mail::to($email_to)->send(new OrderTransportCostMailable($order_data, $pay_data, $money_bag_data));
                    
                }
                
            }

            if($Data['pay_type'] == '5'){

                // update order status to 7 (Already pay)
                $importer = Importer::find($Data['to_ref_id']);

                if($importer){
                    $importer->exchange_rate = $CurrentExchangeRate;
                    $importer->importer_status = 5;
                    $importer->save();

                    $importer_data = Importer::with('customer')
                                    ->where('id', $Data['to_ref_id'])
                                    ->first();

                    $pay_data = MoneyUse::find($Data['id']);

                    $user_id = trim($importer_data->customer->id);
                    $money_bag_data = MoneyBag::where('user_id', $user_id)->first();

                    $email_to = $importer_data->customer->email;

                    Mail::to($email_to)->send(new ImporterCostMailable($importer_data, $pay_data, $money_bag_data));

                }
                
            }


            
            $this->data_result['DATA'] = base64_encode(getUserProfile($Data['user_id']));
            
        }else{
            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'Cannot pay Please try again later.';
        }

        return $this->returnResponse(200, $this->data_result, response(), false);

    }  

    public function payHistory(Request $request){
        $params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_id = trim($user_data['id']);
        $condition = $params['obj']['condition'];
        $currentPage = $params['obj']['currentPage'];
        $limitRowPerPage = $params['obj']['limitRowPerPage'];

        $currentPage = $currentPage - 1;

        $limit = $limitRowPerPage;
        $offset = $currentPage;
        $skip = $offset * $limit;

        $condition['pay_type'] = trim($condition['pay_type']);

        $totalRows = MoneyUse::with('customer')
                    ->join('user', 'user.id', '=', 'money_use.user_id')
                    ->where('user_id', $user_id)
                    ->where(function($query) use ($condition){

                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('user_code', 'LIKE', DB::raw("'" . $condition['keyword'] . "%'"));
                        }

                        if(isset($condition['pay_type']) &&  !empty($condition['pay_type'])){
                            $query->where('pay_type', $condition['pay_type']);
                        }
                        if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                            $condition['created_at'] = getDateFromString($condition['created_at']);
                            $query->where('money_use.created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                        }
                    })
                    ->count();

        $list = MoneyUse::with('customer')
                    ->join('user', 'user.id', '=', 'money_use.user_id')
                    ->where('user_id', $user_id)
                    ->where(function($query) use ($condition){

                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('user_code', 'LIKE', DB::raw("'" . $condition['keyword'] . "%'"));
                        }

                        if(isset($condition['pay_type']) &&  !empty($condition['pay_type'])){
                            $query->where('pay_type', $condition['pay_type']);
                        }
                        if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                            $condition['created_at'] = getDateFromString($condition['created_at']);
                            $query->where('money_use.created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                        }
                    })
                    ->orderBy('money_use.created_at', 'DESC')
                    ->skip($skip)
                    ->take($limit)
                    ->get();

        $this->data_result['DATA']['DataList'] = $list;
        $this->data_result['DATA']['Total'] = $totalRows;

        return $this->returnResponse(200, $this->data_result, response(), false);

    } 

    public function getRequestTopupList(Request $request){

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

        $totalRows = MoneyTopup::with('customer')
                    ->join('user', 'user.id', '=', 'money_topup.user_id')
                    ->where(function($query) use ($condition){

                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('user_code', 'LIKE', DB::raw("'" . $condition['keyword'] . "%'"));
                            $query->orWhere( DB::raw("CONCAT(firstname , ' ', lastname)"), 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        }

                        if(isset($condition['topup_status']) &&  !empty($condition['topup_status'])){
                            $query->where('topup_status', $condition['topup_status']);
                        }
                        if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                            $condition['created_at'] = getDateFromString($condition['created_at']);
                            $query->where('money_topup.created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                        }
                    })
                    ->count();

        $list = MoneyTopup::with('customer')
                    ->select("money_topup.*", 'user.user_code', 'user.firstname', 'user.lastname', 'user.mobile_no')
                    ->join('user', 'user.id', '=', 'money_topup.user_id')
                    ->where(function($query) use ($condition){

                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('user_code', 'LIKE', DB::raw("'" . $condition['keyword'] . "%'"));
                            $query->orWhere( DB::raw("CONCAT(firstname , ' ', lastname)"), 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        }

                        if(isset($condition['topup_status']) &&  !empty($condition['topup_status'])){
                            $query->where('topup_status', $condition['topup_status']);
                        }

                        if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                            $condition['created_at'] = getDateFromString($condition['created_at']);
                            $query->where('money_topup.created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                        }
                    })
                    ->orderBy('topup_date', 'DESC')
                    ->skip($skip)
                    ->take($limit)
                    ->get();

        $this->data_result['DATA']['DataList'] = $list;
        $this->data_result['DATA']['Total'] = $totalRows;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function updateTopupStatus(Request $request){

        $params = $request->all();
        
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];

        $topup_id = trim('' . $params['obj']['topup_id']);
        $topup_status = $params['obj']['topup_status'];

        Log::info("UPDATE_TOPUP_STATUS => ". $params['obj']['topup_status']);
        Log::info("User : " . $user_data['firstname'] . ' ' . $user_data['lastname']);

        $topup = MoneyTopup::with('customer')->find($topup_id);
        
        if($topup){
            // print_r($topup); 
            $topup->topup_status = $topup_status;
            $topup->save();

            if($topup_status == 2){
                $this->approveTopup($topup->user_id, $topup->topup_amount);
            }

            $user_id = trim($topup->customer->id);
            $money_bag_data = MoneyBag::where('user_id', $user_id)->first();

            $email_to = $topup->customer->email;

            Mail::to($email_to)->send(new TopupNotiMailable($topup, $money_bag_data));
            

            $this->data_result['DATA'] = 'Topup success';

        }
        // exit;
        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function updateTransferStatus(Request $request){

        $params = $request->all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];

        $transfer_id = trim($params['obj']['transfer_id']);
        $transfer_status = $params['obj']['transfer_status'];

        Log::info("UPDATE_TRANSFER_STATUS => ". $params['obj']['transfer_status']);
        Log::info("User : " . $user_data['firstname'] . ' ' . $user_data['lastname']);

        $transfer = MoneyUse::with('customer')->find($transfer_id);
        
        if($transfer){
            
            if($transfer_status == 2){

                $current_ex_rate = getLastChinaRateTransfer();

                $transfer_thb = $transfer->pay_amount_yuan * $current_ex_rate;

                $result = $this->approveTransfer($transfer->user_id, $transfer_thb);

                if(!$result){

                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = 'ยอดเงินคงเหลือของลูกค้ารายนี้ไม่พอการชำระโอน';
                }

                $transfer->pay_amount_thb = $transfer_thb;
                $transfer->exchange_rate = $current_ex_rate;
                $transfer->updated_at = date('Y-m-d H:i:s');
                
            }

            $transfer->pay_status = $transfer_status;

            $transfer->save();

            $user_id = trim($transfer->customer->id);
            $money_bag_data = MoneyBag::where('user_id', $user_id)->first();

            $email_to = $transfer->customer->email;

            Mail::to($email_to)->send(new TransferNotiMailable($transfer, $money_bag_data));

            $this->data_result['DATA'] = 'Transfer success';

        }

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function updateDepositStatus(Request $request){

        $params = $request->all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];

        $deposit_id = trim($params['obj']['deposit_id']);
        $deposit_status = $params['obj']['deposit_status'];

        Log::info("UPDATE_DEPOSIT_STATUS => ". $params['obj']['deposit_status']);
        Log::info("User : " . $user_data['firstname'] . ' ' . $user_data['lastname']);

        $deposit = MoneyUse::find($deposit_id);
        
        if($deposit){
            
            if($deposit_status == 2){

                $current_ex_rate = getLastChinaRateTransfer();

                $deposit_thb = $deposit->pay_amount_yuan * $current_ex_rate;

                $result = $this->approveTransfer($deposit->user_id, $deposit_thb);

                if(!$result){

                    $this->data_result['STATUS'] = 'ERROR';
                    $this->data_result['DATA'] = 'ยอดเงินคงเหลือของลูกค้ารายนี้ไม่พอการชำระฝากจ่าย';
                }

                $deposit->pay_amount_thb = $deposit_thb;
                $deposit->exchange_rate = $current_ex_rate;
                $deposit->updated_at = date('Y-m-d H:i:s');

            }

            $deposit->pay_status = $deposit_status;

            $deposit->save();

            $user_id = trim($deposit->customer->id);
            $money_bag_data = MoneyBag::where('user_id', $user_id)->first();

            $email_to = $deposit->customer->email;

            Mail::to($email_to)->send(new DepositNotiMailable($deposit, $money_bag_data));

            $this->data_result['DATA'] = 'Deposit success';

        }

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function getPayList(Request $request){

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

        $condition['user_id'] = trim($condition['user_id']);
        $condition['pay_type'] = trim($condition['pay_type']);

        $totalRows = MoneyUse::with('customer')
                    ->join('user', 'user.id', '=', 'money_use.user_id')
                    ->where('pay_status', 2)
                    ->where(function($query) use ($condition){
                        if(isset($condition['user_id']) &&  !empty($condition['user_id'])){
                            $query->where('user_id', $condition['user_id']);
                        }

                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('user_code', 'LIKE', DB::raw("'" . $condition['keyword'] . "%'"));
                            $query->orWhere( DB::raw("CONCAT(firstname , ' ', lastname)"), 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        }

                        if(isset($condition['pay_type']) &&  !empty($condition['pay_type'])){
                            $query->where('pay_type', $condition['pay_type']);
                        }
                        if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                            $condition['created_at'] = getDateFromString($condition['created_at']);
                            $query->where('money_use.created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                        }
                    })
                    ->count();

        $list = MoneyUse::with('customer')
                    ->select("money_use.*", 'user.user_code', 'user.firstname', 'user.lastname')
                    ->join('user', 'user.id', '=', 'money_use.user_id')
                    ->where('pay_status', 2)
                    ->where(function($query) use ($condition){
                        if(isset($condition['user_id']) &&  !empty($condition['user_id'])){
                            $query->where('user_id', $condition['user_id']);
                        }

                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('user_code', 'LIKE', DB::raw("'" . $condition['keyword'] . "%'"));
                            $query->orWhere( DB::raw("CONCAT(firstname , ' ', lastname)"), 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        }

                        if(isset($condition['pay_type']) &&  !empty($condition['pay_type'])){
                            $query->where('pay_type', $condition['pay_type']);
                        }
                        if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                            $condition['created_at'] = getDateFromString($condition['created_at']);
                            $query->where('money_use.created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                        }
                    })
                    ->orderBy('money_use.created_at', 'DESC')
                    ->skip($skip)
                    ->take($limit)
                    ->get();

        $this->data_result['DATA']['DataList'] = $list;
        $this->data_result['DATA']['Total'] = $totalRows;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function getTransferList(Request $request){

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

        $condition['user_id'] = trim($condition['user_id']);
        $condition['pay_status'] = trim($condition['pay_status']);

        $totalRows = MoneyUse::with('customer')
                    ->join('user', 'user.id', '=', 'money_use.user_id')
                    ->where('pay_type', 3)
                    ->where(function($query) use ($condition){
                        if(isset($condition['user_id']) &&  !empty($condition['user_id'])){
                            $query->where('user_id', $condition['user_id']);
                        }

                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('user_code', 'LIKE', DB::raw("'" . $condition['keyword'] . "%'"));
                            $query->orWhere( DB::raw("CONCAT(firstname , ' ', lastname)"), 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        }

                        if(isset($condition['pay_status']) &&  !empty($condition['pay_status'])){
                            $query->where('pay_status', $condition['pay_status']);
                        }
                        if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                            $condition['created_at'] = getDateFromString($condition['created_at']);
                            $query->where('money_use.created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                        }
                    })
                    ->count();

        $list = MoneyUse::with('customer')
                    ->select("money_use.*", 'user.user_code', 'user.firstname', 'user.lastname')
                    ->join('user', 'user.id', '=', 'money_use.user_id')
                    ->where('pay_type', 3)
                    ->where(function($query) use ($condition){
                        if(isset($condition['user_id']) &&  !empty($condition['user_id'])){
                            $query->where('user_id', $condition['user_id']);
                        }

                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('user_code', 'LIKE', DB::raw("'" . $condition['keyword'] . "%'"));
                            $query->orWhere( DB::raw("CONCAT(firstname , ' ', lastname)"), 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        }

                        if(isset($condition['pay_status']) &&  !empty($condition['pay_status'])){
                            $query->where('pay_status', $condition['pay_status']);
                        }
                        if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                            $condition['created_at'] = getDateFromString($condition['created_at']);
                            $query->where('money_use.created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                        }
                    })
                    ->orderBy('money_use.created_at', 'DESC')
                    ->skip($skip)
                    ->take($limit)
                    ->get();

        $this->data_result['DATA']['DataList'] = $list;
        $this->data_result['DATA']['Total'] = $totalRows;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function getDepositList(Request $request){

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

        $condition['user_id'] = trim($condition['user_id']);
        $condition['pay_status'] = trim($condition['pay_status']);

        $totalRows = MoneyUse::with('customer')
                    ->join('user', 'user.id', '=', 'money_use.user_id')
                    ->where('pay_type', 4)
                    ->where(function($query) use ($condition){
                        if(isset($condition['user_id']) &&  !empty($condition['user_id'])){
                            $query->where('user_id', $condition['user_id']);
                        }

                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('user_code', 'LIKE', DB::raw("'" . $condition['keyword'] . "%'"));
                            $query->orWhere( DB::raw("CONCAT(firstname , ' ', lastname)"), 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        }

                        if(isset($condition['pay_status']) &&  !empty($condition['pay_status'])){
                            $query->where('pay_status', $condition['pay_status']);
                        }
                        if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                            $condition['created_at'] = getDateFromString($condition['created_at']);
                            $query->where('money_use.created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                        }
                    })
                    ->count();

        $list = MoneyUse::with('customer')
                    ->select("money_use.*", 'user.user_code', 'user.firstname', 'user.lastname')
                    ->join('user', 'user.id', '=', 'money_use.user_id')
                    ->where('pay_type', 4)
                    ->where(function($query) use ($condition){
                        if(isset($condition['user_id']) &&  !empty($condition['user_id'])){
                            $query->where('user_id', $condition['user_id']);
                        }

                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('user_code', 'LIKE', DB::raw("'" . $condition['keyword'] . "%'"));
                            $query->orWhere( DB::raw("CONCAT(firstname , ' ', lastname)"), 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        }

                        if(isset($condition['pay_status']) &&  !empty($condition['pay_status'])){
                            $query->where('pay_status', $condition['pay_status']);
                        }
                        if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                            $condition['created_at'] = getDateFromString($condition['created_at']);
                            $query->where('money_use.created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                        }
                    })
                    ->orderBy('money_use.created_at', 'DESC')
                    ->skip($skip)
                    ->take($limit)
                    ->get();

        $this->data_result['DATA']['DataList'] = $list;
        $this->data_result['DATA']['Total'] = $totalRows;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function withdrawnMoney(Request $request){

        $params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $admin_id = $user_data['id'];
        $WithdrawnData = $params['obj']['WithdrawnData'];
        $WithdrawnData['id'] = trim($WithdrawnData['id']);

        $money_bag = MoneyBag::where('user_id', $WithdrawnData['id'])->first();
        
        if($money_bag){

            $money_bag->balance = $money_bag->balance - floatval($WithdrawnData['money_bags']['withdrawn_amount']);
            
            if($money_bag->balance < 0){
                $this->data_result['STATUS'] = 'ERROR';
                $this->data_result['DATA'] = 'ไม่สามารถถอนเงินได้ เนื่องจากยอดเงินไม่เพียงพอ';

                return $this->returnResponse(200, $this->data_result, response(), false);
            }

            $money_bag->save();

            // update log
            $withdrawn_history = new WithdrawnHistory();
            $withdrawn_history->id = generateID();
            $withdrawn_history->user_id = trim($WithdrawnData['id']);
            $withdrawn_history->withdrawn_amount = $WithdrawnData['money_bags']['withdrawn_amount'];
            $withdrawn_history->withdrawn_by = $admin_id;

            $withdrawn_history->save();
        }

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function refundMoney(Request $request){

        $params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $admin_id = $user_data['id'];
        $RefundData = $params['obj']['RefundData'];
        $RefundData['id'] = trim($RefundData['id']);

        $money_bag = MoneyBag::where('user_id', $RefundData['id'])->first();
        
        if($money_bag){

            $money_bag->balance = $money_bag->balance + floatval($RefundData['money_bags']['refund_amount']);

            $money_bag->save();

            // update log
            $refund_history = new RefundHistory();
            $refund_history->id = generateID();
            $refund_history->user_id = trim($RefundData['id']);
            $refund_history->refund_amount = $RefundData['money_bags']['refund_amount'];
            $refund_history->refund_by = $admin_id;

            $refund_history->save();
        }

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function getRefundList(Request $request){

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

        $totalRows = RefundHistory::join('user', 'user.id', '=', 'refund_history.user_id')
                    ->where(function($query) use ($condition){

                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('user_code', 'LIKE', DB::raw("'" . $condition['keyword'] . "%'"));
                            $query->orWhere( DB::raw("CONCAT(user.firstname , ' ', user.lastname)"), 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        }

                        if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                            $condition['created_at'] = getDateFromString($condition['created_at']);
                            $query->where('refund_history.created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                        }

                    })
                    ->count();

        $list = RefundHistory::select("refund_history.*", 'user.user_code', 'user.firstname', 'user.lastname', 'user.mobile_no'
                        , DB::raw('user_admin.firstname AS admin_firstname'), DB::raw('user_admin.lastname AS admin_lastname'))
                    ->join('user', 'user.id', '=', 'refund_history.user_id')
                    ->join('user_admin', 'user_admin.id', '=', 'refund_history.refund_by')
                    ->where(function($query) use ($condition){
                        
                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('user_code', 'LIKE', DB::raw("'" . $condition['keyword'] . "%'"));
                            $query->orWhere( DB::raw("CONCAT(user.firstname , ' ', user.lastname)"), 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        }

                        if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                            $condition['created_at'] = getDateFromString($condition['created_at']);
                            $query->where('refund_history.created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                        }

                    })
                    ->orderBy('refund_history.created_at', 'DESC')
                    ->skip($skip)
                    ->take($limit)
                    ->get();

        $this->data_result['DATA']['DataList'] = $list;
        $this->data_result['DATA']['Total'] = $totalRows;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function getWithdrawnList(Request $request){

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

        $totalRows = WithdrawnHistory::join('user', 'user.id', '=', 'withdrawn_history.user_id')
                    ->where(function($query) use ($condition){

                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('user_code', 'LIKE', DB::raw("'" . $condition['keyword'] . "%'"));
                            $query->orWhere( DB::raw("CONCAT(user.firstname , ' ', user.lastname)"), 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        }

                        if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                            $condition['created_at'] = getDateFromString($condition['created_at']);
                            $query->where('withdrawn_history.created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                        }

                    })
                    ->count();

        $list = WithdrawnHistory::select("withdrawn_history.*", 'user.user_code', 'user.firstname', 'user.lastname', 'user.mobile_no'
                        , DB::raw('user_admin.firstname AS admin_firstname'), DB::raw('user_admin.lastname AS admin_lastname'))
                    ->join('user', 'user.id', '=', 'withdrawn_history.user_id')
                    ->join('user_admin', 'user_admin.id', '=', 'withdrawn_history.withdrawn_by')
                    ->where(function($query) use ($condition){
                        
                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('user_code', 'LIKE', DB::raw("'" . $condition['keyword'] . "%'"));
                            $query->orWhere( DB::raw("CONCAT(user.firstname , ' ', user.lastname)"), 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        }

                        if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                            $condition['created_at'] = getDateFromString($condition['created_at']);
                            $query->where('withdrawn_history.created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                        }

                    })
                    ->orderBy('withdrawn_history.created_at', 'DESC')
                    ->skip($skip)
                    ->take($limit)
                    ->get();

        $this->data_result['DATA']['DataList'] = $list;
        $this->data_result['DATA']['Total'] = $totalRows;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    private function approveTopup($user_id, $topup_amount){

        $money_bag = MoneyBag::where('user_id', $user_id)->first();
        
        if(empty($money_bag)){

            $money_bag = new MoneyBag();
            $money_bag->id = generateID();
            $money_bag->user_id = $user_id;
            $money_bag->balance = 0;
        }

        // update balance
        $money_bag->balance += $topup_amount;

        $money_bag->save();

    }

    private function approveTransfer($user_id, $transter_amount){

        $result = false;
        $money_bag = MoneyBag::where('user_id', $user_id)->first();
        
        if(!empty($money_bag)){

            // update balance
            $money_bag->balance -= $transter_amount;

            if($money_bag->balance >= 0){

                $result = true;
                $money_bag->save();    

            }
        }

        return $result;

    }

    private function reCalcMoneyBagBalance($user_id, $pay_amount_thb){

        $money_bag = MoneyBag::where('user_id', $user_id)->first();
        
        if(!empty($money_bag)){

            $money_bag->balance = $money_bag->balance - $pay_amount_thb;
            $money_bag->save();

        }

        return $money_bag->balance;
    }

}
