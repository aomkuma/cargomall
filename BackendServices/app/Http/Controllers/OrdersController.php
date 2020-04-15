<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Order;
use App\OrderDesc;
use App\OrderDetail;
use App\CartSession;
use Request;

use DB;

use App\Mail\OrderRequestMailable;

use Mail;

use Excel;

use App\Imports\OrdersImport;

use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    //
    public function pendingList(){

        $params = Request::all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        
        $list = Order::with('customer')
                ->whereIn('order_status', [2,7])
                ->orderBy('created_at', 'DESC')
                ->get();

        $this->data_result['DATA'] = $list;
        
        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function list(){

        $params = Request::all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $condition = $params['obj']['condition'];
        $currentPage = $params['obj']['currentPage'];
        $limitRowPerPage = $params['obj']['limitRowPerPage'];

        $currentPage = $currentPage - 1;

        $limit = $limitRowPerPage;
        $offset = $currentPage;
        $skip = $offset * $limit;

        $totalRows = Order::with('orderDesc')
                ->where(function($query) use ($condition){
                    if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                        $query->where('order_no' , 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        $query->orWhere('tracking_no' , 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                    }
                    if(isset($condition['order_status']) &&  !empty($condition['order_status'])){
                        $query->where('order_status', $condition['order_status']);
                    }else{
                        $query->where('order_status', '<>', 9);
                    }

                    if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                        $condition['created_at'] = getDateFromString($condition['created_at']);
                        $query->where('created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                    }
                })
                ->count();

        $list = Order::with('orderDesc')
                ->with('customer')
                ->where(function($query) use ($condition){
                    if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                        $query->where('order_no' , 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        $query->orWhere('tracking_no' , 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                    }
                    if(isset($condition['order_status']) &&  !empty($condition['order_status'])){
                        $query->where('order_status', $condition['order_status']);
                    }else{
                        $query->where('order_status', '<>', 9);
                    }

                    if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                        $condition['created_at'] = getDateFromString($condition['created_at']);
                        $query->where('created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                    }
                })
                ->orderBy('created_at', 'DESC')
                ->skip($skip)
                ->take($limit)
                ->get();

        $this->data_result['DATA']['DataList'] = $list;
        $this->data_result['DATA']['Total'] = $totalRows;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function get(){
        $params = Request::all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $order_id = $params['obj']['order_id'];

        $order = Order::with('orderDesc')
                    ->with('orderDetails')
                    ->with('customer')
                    ->with('customerAddress')
                    ->with('orderTrackings')
                    ->where('id', $order_id)
                    ->first();

        $this->data_result['DATA'] = $order;

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function listByUser(){

        $params = Request::all();

        $user_data = [];
        if(isset($params['user_session']['user_data'])){
            $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
            $user_data['id'] = ''.$user_data['id'];
        }else{
            $user_data['id'] = null;
        }
        $condition = $params['obj']['condition'];

        $list = Order::with('orderDesc')
                ->with('orderDetails')
                ->where('user_id', $user_data['id'])
                ->where(function($query) use ($condition){
                    if(isset($condition['pay_type']) && $condition['pay_type'] == 1){
                        $query->where('order_status' , 1);    
                    }else if(isset($condition['pay_type']) && $condition['pay_type'] == 2){
                        $query->where('order_status' , 6);
                    }   

                    if(isset($condition['to_ref_id']) &&  !empty($condition['to_ref_id'])){
                        $query->where('id' , $condition['to_ref_id']);
                    }

                    if(isset($condition['order_no']) && !empty($condition['order_no'])){
                        $query->where('order_no' , $condition['order_no']);
                    }
                    if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                        $condition['created_at'] = getDateFromString($condition['created_at']);
                        $query->where('created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                    }
                })
                ->get();

        $this->data_result['DATA'] = $list;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function confirmOrder(){

    	$params = Request::all();

    	$user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];

        $user_id = $user_data['id'];

    	$ProductList = $params['obj']['ProductList'];
    	$ShippingOptions = $params['obj']['ShippingOptions'];
    	// print_r($user_data['id']);exit;

        $cost = $this->calcCost($ProductList);

        $money_balance = checkAccountBalance($user_data['id']);

        if($money_balance < $cost){

            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'ยอดเงินคงเหลือของคุณไม่พอสำหรับการทำรายการสั่งซื้อ กรุณาเติมเงินแล้วกลับมาทำการยืนยันอีกคร้ัง';

            return $this->returnResponse(200, $this->data_result, response(), false);

        }

    	$order_id = generateID();
    	$order_no = generateOrderNo();

    	$order = new Order();
    	$order->id = $order_id;
    	$order->user_id = $user_data['id'];
    	$order->order_no = $order_no;
    	
    	$order->net_price = $cost;
    	$order->estimate_cost = $cost;
    	$order->transport_type = $ShippingOptions['transport_type'];
    	$order->payment_type = 'TRANSFER';
    	$order->receive_order_type = $ShippingOptions['receive_order_type'];

    	if(!empty($ShippingOptions['special_option'])){
    		$order->add_on = $ShippingOptions['special_option'];//implode(',', $ShippingOptions['special_option']);
    	}

    	if(!empty($ShippingOptions['customer_address_id'])){
    		$order->customer_address_id = $ShippingOptions['customer_address_id'];
    	}
    	
    	$order->transport_company = $ShippingOptions['transport_company'];

        if($order->transport_company == 'other'){
            $ShippingOptions['transport_company_other'] = null;
        }

        if(isset($ShippingOptions['transport_company_other'])){
            $order->transport_company_other = $ShippingOptions['transport_company_other'];
        }
    	$order->order_status = 1;

    	$order->save();

    	// save order description
    	$order_desc = new OrderDesc();
    	$order_desc->id = generateID();
    	$order_desc->order_id = $order_id;
    	$order_desc->china_ex_rate = 0;    //  $ProductList[0]['exchange_rate'];

    	$order_desc->save();

    	// save products
    	foreach ($ProductList as $k => $v) {

    		$order_detail = new OrderDetail();

    		$order_detail->id = generateID();
    		$order_detail->order_id = $order_id;
    		$order_detail->product_original_url = $v['product_url'];
    		$order_detail->product_thumbail_path = $v['product_image'];
			$order_detail->product_name = $v['product_original_name'];
			$order_detail->product_price_yuan = $v['product_normal_price'];
			$order_detail->product_price_thb = $v['product_normal_price'] * $v['exchange_rate'];
			$order_detail->product_promotion_price = $v['product_promotion_price'];
			$order_detail->product_promotion_price_thb =  ($v['product_promotion_price'] * $v['exchange_rate']);                        
            $order_detail->product_choose_amount = $v['product_qty'];

            if(!empty($v['product_color_img_choose'])){
                $order_detail->product_choose_color_img = $v['product_color_img_choose'];    
            }
			
			
			if(!empty($v['product_color_img_reserve'])){
				$order_detail->product_reserve_color_img = $v['product_color_img_reserve'];
			}

            if(!empty($v['product_color_choose'])){
                $order_detail->product_choose_color = $v['product_color_choose'];    
            }
			
			if(!empty($v['product_color_reserve'])){
				$order_detail->product_reserve_color = $v['product_color_reserve'];	
			}
			
            if(!empty( $v['product_size_choose'])){
                $order_detail->product_choose_size = $v['product_size_choose'];
            }

			if(!empty($v['product_size_reserve'])){
				$order_detail->product_reserve_size = $v['product_size_reserve'];
			}

            if(!empty($v['remark'])){
    			$order_detail->remark = $v['remark'];
            }
    		
    		$order_detail->save();

    	}

        $cart = CartSession::where('user_id', $user_id)->delete();

        $this->data_result['DATA']['OrderID'] = $order_id;
    	$this->data_result['DATA']['OrderNumber'] = $order_no;

    	return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function updateOrderStatus(){

        $params = Request::all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $order_id = trim($params['obj']['order_id']);
        $to_order_status = trim($params['obj']['to_order_status']);

        Log::info("UPDATE_ORDER_STATUS => ");
        Log::info("User : " . $user_data['firstname'] . ' ' . $user_data['lastname']);

        $order = Order::with('customer')->find($order_id);
        if($order){

            $current_order_status = $order->order_status;

            $order->updated_at = date('Y-m-d H:i:s');
            $order->order_status = $to_order_status;
            $order->save();

            Log::info("Order No : " . $order->order_no);
            Log::info("To Order Status : " . $to_order_status);

            if($to_order_status == 6 && $current_order_status < $to_order_status){
                $mobile_no = $order->customer->mobile_no;
                $message = 'รายการฝากสั่งเลขที่ ' . $order->order_no . ' ขณะนี้อยู่ในสถานะ "รอการชำระค่าขนส่ง" กรุณาเข้าสู่ระบบเพื่อดำเนินการชำระค่าขนส่งสินค้า ขอบคุณค่ะ';
                sendSms($mobile_no, $message);
            }

            $this->data_result['DATA'] = true;
        }else{

            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'Order not found';

        }

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function testSendMail(){
        $order_data = Order::with('orderDesc')
                    ->with('orderDetails')
                    ->with('customer')
                    ->with('customerAddress')
                    ->where('id', '6575924561202002')
                    ->first();

        if($order_data->orderDesc->china_ex_rate == 0){
            $order_data->orderDesc->china_ex_rate = getLastChinaRate();
        }

        $email_to = $order_data->customer->email;

        Mail::to($email_to)->send(new OrderRequestMailable($order_data));
    }

    public function updateOrder(){

        $params = Request::all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $Order = $params['obj']['Order'];
        $OrderDesc = $params['obj']['OrderDesc'];
        
        Log::info("UPDATE_ORDER => ");
        Log::info("User : " . $user_data['firstname'] . ' ' . $user_data['lastname']);
        Log::info("Order No : " . $Order['order_no']);
        
        $Order['id'] = trim($Order['id']);

        $order = Order::find($Order['id']);
        if($order){
            $order->tracking_no = $Order['tracking_no'];
            $order->tracking_no_thai = $Order['tracking_no_thai'];
            $order->discount = $Order['discount'];
            $order->remark = $Order['remark'];

            $order->save();

            $this->data_result['DATA'] = true;
        }

        $OrderDesc['id'] = trim($OrderDesc['id']);
        $order_desc = OrderDesc::find($OrderDesc['id']);
        
        if($order_desc){

            $order_desc->total_china_transport_cost = $OrderDesc['total_china_transport_cost'];
            $order_desc->china_thai_transport_cost = $OrderDesc['china_thai_transport_cost'];
            $order_desc->transport_company_cost = $OrderDesc['transport_company_cost'];
            $order_desc->save();

        }

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function cancelOrder(){

        $params = Request::all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $order_id = trim($params['obj']['order_id']);
        
        Log::info("CANCEL_ORDER => ");
        Log::info("By User : " . $user_data['firstname'] . ' ' . $user_data['lastname']);

        $order = Order::find($order_id);

        if($order){

            Log::info("Order No : " . $order->order_no);
            $order->order_status = 9;
            $order->save();

            $this->data_result['DATA'] = true;
        }

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function uploadExcel(){

        $file = Request::file();
        $AttachFile = $file['obj']['AttachFile'];

        // $landing_path = $AttachFile->storeAs(
        //             'order', 'order__'. date('YmdHis')  . '.' . $AttachFile->getClientOriginalExtension()
        //         );

        // echo "Begin ";
        Excel::import(new OrdersImport, $AttachFile);
        // echo "Result ";
        // var_dump($arr);
        // exit;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function updateTransportCompany(){

        $params = Request::all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $Order = $params['obj']['Order'];

        $Order['id'] = trim($Order['id']);

        $order = Order::find($Order['id']);
        if($order){
            $order->transport_company = $Order['transport_company'];

            if($Order['transport_company'] != 'other'){
                $Order['transport_company_other'] = null;   
            }
            $order->transport_company_other = $Order['transport_company_other'];
            $order->save();

            $this->data_result['DATA'] = true;
        }

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    private function calcCost($ProductList){

    	$cost = 0;
    	foreach ($ProductList as $k => $v) {
    		if($v['product_promotion_price'] > 0){
    			$cost += ($v['product_promotion_price'] * /*$v['exchange_rate']) **/ $v['product_qty']);
    		}else{
    			$cost += ($v['product_normal_price'] * /*$v['exchange_rate']) **/ $v['product_qty']);
    		}
    	}

    	return $cost;
    }


}
