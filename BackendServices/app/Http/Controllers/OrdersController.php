<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Order;
use App\OrderDesc;
use App\OrderDetail;
use App\OrderTracking;
use App\CartSession;
use App\Models\OrderActivityLog;

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

    public function deleteProduct(){

        $params = Request::all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $order_detail_id = $params['obj']['order_detail_id'];
        
        $order_activity_log = OrderDetail::find($order_detail_id)->delete();

        $this->data_result['DATA']['order_activity_log'] = $order_activity_log;
        
        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function getHistoryList(){

        $params = Request::all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $order_id = $params['obj']['order_id'];
        
        $order_activity_log = OrderActivityLog::where('order_id', $order_id)
                            ->orderBy('created_at', 'DESC')
                            ->get();

        $this->data_result['DATA']['order_activity_log'] = $order_activity_log;
        
        return $this->returnResponse(200, $this->data_result, response(), false);

    }
    
    public function updateProductList(){

        $params = Request::all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $ProductList = $params['obj']['ProductList'];
        $result = 0;

        $cost = 0;

        foreach ($ProductList as $key => $value) {
            $update_data = [];
            $update_data['product_price_yuan'] = $value['product_price_yuan'];
            $update_data['product_promotion_price'] = $value['product_promotion_price'];
            $update_data['product_choose_amount'] = $value['product_choose_amount'];

            $result += OrderDetail::find($value['id'])->update($update_data);

            if($value['product_promotion_price'] > 0){
                $cost += ($value['product_promotion_price'] * /*$v['exchange_rate']) **/ $value['product_choose_amount']);
            }else{
                $cost += ($value['product_price_yuan'] * /*$v['exchange_rate']) **/ $value['product_choose_amount']);
            }
        }

        $order_id = $ProductList[0]['order_id'];

        // update price total
        // $cost = $this->calcCost($ProductList);
        $order_data = Order::find($order_id);
        $order_data->net_price = $cost;
        $order_data->save();

        // $order_no = $order->order_no;
        $admin_id = $user_data['id'];
        $admin_name = $user_data['firstname'] . ' ' . $user_data['lastname'];

        addOrderActivityLog('update_product', $order_id, $admin_id, $admin_name, null, null, null, null, null);

        $this->data_result['DATA']['result'] = $result;
        
        return $this->returnResponse(200, $this->data_result, response(), false);

    }

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

        if(isset($condition['order_status']) && $condition['order_status'] == 0){
            $condition['order_status'] = '0';
        }

        $totalRows = Order::with('orderDesc')
                ->join('user' , 'user.id', '=', 'order.user_id')
                ->where(function($query) use ($condition){
                    if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                        $query->where('order_no' , 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        $query->orWhere('tracking_no' , 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        $query->orWhere('user_code' , 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                    }
                    if(isset($condition['order_status']) &&  (!empty($condition['order_status']) || $condition['order_status'] == '0')){
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


        $list = Order::select('order.*')
                ->with('orderDesc')
                ->with('customer')
                ->join('user' , 'user.id', '=', 'order.user_id')
                ->where(function($query) use ($condition){
                    if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                        $query->where('order_no' , 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        $query->orWhere('tracking_no' , 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        $query->orWhere('user_code' , 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                    }
                    if(isset($condition['order_status']) &&  (!empty($condition['order_status']) || $condition['order_status'] == '0')){
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

                    if(isset($condition['order_status']) && !empty($condition['order_status'])){
                        $query->where('order_status' , $condition['order_status']);
                    }
                    else if(isset($condition['pay_type']) && $condition['pay_type'] == 1){
                        $query->where('order_status' , 1);    
                    }
                    else if(isset($condition['pay_type']) && $condition['pay_type'] == 2){
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
                ->orderBy('created_at', 'DESC')
                ->get();

        $this->data_result['DATA'] = $list;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function listByUserLimit(){

        $params = Request::all();

        $user_data = [];
        if(isset($params['user_session']['user_data'])){
            $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
            $user_data['id'] = ''.$user_data['id'];
        }else{
            $user_data['id'] = null;
        }
        // $condition = $params['obj']['condition'];

        $list = Order::where('user_id', $user_data['id'])
                ->orderBy('created_at', 'DESC')
                ->take(5)
                ->get();

        $this->data_result['DATA'] = $list;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function listByUserAndStatus(){

        $params = Request::all();

        $user_data = [];
        if(isset($params['user_session']['user_data'])){
            $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
            $user_data['id'] = ''.$user_data['id'];
        }else{
            $user_data['id'] = null;
        }
        $condition = $params['obj']['condition'];

        if($condition['pay_type'] == 1){

            $list = Order::with('orderDesc')
                    ->with('orderDetails')
                    ->where('user_id', $user_data['id'])
                    ->where(function($query) use ($condition){
                        if(isset($condition['pay_type']) && $condition['pay_type'] == 1){
                            $query->where('order_status' , 1);    
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

        }else if($condition['pay_type'] == 2){
            $list = OrderTracking::select("order_tracking.*", "order.id", "order.order_no", "order.created_at", "order.user_id")
                        ->join("order", "order.id", "=", "order_tracking.order_id")
                        ->where('order.user_id', $user_data['id'])
                        ->whereNotNull('order_tracking.transport_cost_china')
                        ->whereNotNull('order_tracking.transport_cost_thai')
                        ->whereNotNull('order_tracking.import_fee')

                        ->where('order_tracking.transport_cost_china' , '>=', 0)
                        ->where('order_tracking.transport_cost_thai' , '>=', 0)
                        ->where('order_tracking.import_fee' , '>=', 0)

                        ->where('order_tracking.payment_status', false)
                        ->where(function($query) use ($condition){
                            if(isset($condition['pay_type']) && $condition['pay_type'] == 2){
                                $query->where('order.order_status' , 6);
                            }   

                            if(isset($condition['to_ref_id']) &&  !empty($condition['to_ref_id'])){
                                $query->where('order.id' , $condition['to_ref_id']);
                            }

                            if(isset($condition['order_no']) && !empty($condition['order_no'])){
                                $query->where('order.order_no' , $condition['order_no']);
                            }
                            if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                                $condition['created_at'] = getDateFromString($condition['created_at']);
                                $query->where('order.created_at', 'LIKE', DB::raw("'" . $condition['created_at'] . "%'"));
                            }
                        })
                        ->get();
        }

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

        // $money_balance = checkAccountBalance($user_data['id']);

        // if($money_balance < $cost){

        //     $this->data_result['STATUS'] = 'ERROR';
        //     $this->data_result['DATA'] = 'ยอดเงินคงเหลือของคุณไม่พอสำหรับการทำรายการสั่งซื้อ กรุณาเติมเงินแล้วกลับมาทำการยืนยันอีกคร้ัง';

        //     return $this->returnResponse(200, $this->data_result, response(), false);

        // }

    	$order_id = generateID();

        $max_running = Order::count() + 1;
    	$order_no = generateOrderNo($max_running);

    	$order = new Order();
    	$order->id = $order_id;
    	$order->user_id = $user_data['id'];
    	$order->order_no = $order_no;
    	
    	$order->net_price = $cost;
    	$order->estimate_cost = $cost;
    	$order->transport_type = $ShippingOptions['transport_type'];
        $order->package_type = $ShippingOptions['package_type'];
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
    	$order->order_status = 0;

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

            /*if($to_order_status == 6 && $current_order_status < $to_order_status){
                $mobile_no = $order->customer->mobile_no;
                $message = 'รายการฝากสั่งเลขที่ ' . $order->order_no . ' ขณะนี้อยู่ในสถานะ "รอการชำระค่าขนส่ง" กรุณาเข้าสู่ระบบเพื่อดำเนินการชำระค่าขนส่งสินค้า ขอบคุณค่ะ';
                sendSms($mobile_no, $message);
            }*/

            $order_id = $order->id;
            $order_no = $order->order_no;
            $admin_id = $user_data['id'];
            $admin_name = $user_data['firstname'] . ' ' . $user_data['lastname'];
            $old_status = $current_order_status;
            $new_status = $to_order_status;

            addOrderActivityLog('update_status', $order_id, $admin_id, $admin_name, $order_no, $old_status, $new_status, null, null);

            $this->data_result['DATA'] = true;
        }else{

            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'Order not found';

        }

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function sendTransportPaymentSMS(){
        $params = Request::all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $order_id = trim($params['obj']['order_id']);

        $order = Order::with('customer')->find($order_id);

        if($order && $order->order_status == 6){

            $mobile_no = $order->customer->mobile_no;

            // get waiting payment tracking status
            $tracking_no_list = OrderTracking::select('tracking_no')
                                ->where('order_id', $order_id)
                                ->whereNotNull('order_tracking.transport_cost_china')
                                ->whereNotNull('order_tracking.transport_cost_thai')
                                ->whereNotNull('order_tracking.import_fee')
                                ->where('order_tracking.transport_cost_china' , '>=', 0)
                                ->where('order_tracking.transport_cost_thai' , '>=', 0)
                                ->where('order_tracking.import_fee' , '>=', 0)
                                ->where('order_tracking.payment_status', false)
                                ->get()->toArray();

            // print_r($tracking_no_list);exit;
            $track_arr = [];
            foreach ($tracking_no_list as $key => $value) {
                $track_arr[] = $value['tracking_no'];
            }

            $message = 'รายการฝากสั่ง ' . implode(',', $track_arr) . ' ขณะนี้อยู่ในสถานะ "รอการชำระค่าขนส่ง" กรุณาเข้าสู่ระบบเพื่อดำเนินการชำระค่าขนส่งสินค้า ขอบคุณค่ะ';
            sendSms($mobile_no, $message);

            $this->data_result['DATA'] = true;
        
        }else{

            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'Order not found';

        }

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function sendProductPaymentSMS(){
        $params = Request::all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $order_id = trim($params['obj']['order_id']);

        $order = Order::with('customer')->find($order_id);

        if($order && $order->order_status == 1){

            $mobile_no = $order->customer->mobile_no;

            $message = 'รายการฝากสั่ง เลขที่ ' . $order->order_no . ' ขณะนี้อยู่ในสถานะ "รอการชำระเงินค่าสินค้า" กรุณาเข้าสู่ระบบเพื่อดำเนินการชำระค่าสินค้า ขอบคุณค่ะ';
            sendSms($mobile_no, $message);

            $this->data_result['DATA'] = true;
        
        }else{

            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'Order not found';

        }

        return $this->returnResponse(200, $this->data_result, response(), false);

    }


    public function sendPaymentLine(Request $req){
        
        $params = Request::all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $order_id = trim($params['obj']['order_id']);
        $line_admin_remark = $params['obj']['line_admin_remark'];
        $order = Order::with('customer')->find($order_id);

        $send_result = false;

        $ACCESS_TOKEN = env('LINE_ACCESS_TOKEN', '7z3T93tZJwQq+8icmJA4hNrefidb0FW5eVWldjX7aHEGbfrubUJ/FEsJL9GAupmMHlnLO0kg7EZlEWm4ymDwJhuZDf8NRxdLeALSun5dXt5vtnVsTtaeV7VEQrnEcpKOfJztvzonaJFCu/f2oAsR2QdB04t89/1O/w1cDnyilFU=');
        $CHANEL_SECRET = env('LINE_CHANEL_SECRET', '4a78b86e4bd85c783f4697deba5d0bc3');
        
        $POST_HEADER = array('Content-Type: application/json', 'Authorization: Bearer ' . $ACCESS_TOKEN);
        $message_type = 'text';
        $API_URL = env('LINE_PUSH_MASSAGE_URL', 'https://api.line.me/v2/bot/message/push');
        

        if(!empty($order->customer->line_user_id)){
            if($order && $order->order_status == 1){

                $message_desc = 'รายการฝากสั่ง เลขที่ ' . $order->order_no . " \r\nขณะนี้อยู่ในสถานะ \"รอการชำระเงินค่าสินค้า\"\r\nกรุณาเข้าสู่ระบบ เพื่อดำเนินการชำระค่าสินค้า ขอบคุณค่ะ";
                // sendSms($mobile_no, $message);
                // $to_user_id = $order->customer->line_user_id;

                // // $line_config = LineConfigItem::where('id', $line_config_item_id)->first();
                // $send_data = [];
                // $send_data['to'] = $to_user_id;
                // $send_data['messages'] = [['type' => $message_type, 'text' => $message_desc]];

                // $post_body = json_encode($send_data, JSON_UNESCAPED_UNICODE);
                // $this->data_result['DATA'] = true;
                // $send_result = $this->send_line_message($API_URL, $POST_HEADER, $post_body);
            
            }else if($order && $order->order_status == 6){

                $mobile_no = $order->customer->mobile_no;

                // get waiting payment tracking status
                $tracking_no_list = OrderTracking::select('tracking_no')
                                    ->where('order_id', $order_id)
                                    ->whereNotNull('order_tracking.transport_cost_china')
                                    ->whereNotNull('order_tracking.transport_cost_thai')
                                    ->whereNotNull('order_tracking.import_fee')
                                    ->where('order_tracking.transport_cost_china' , '>=', 0)
                                    ->where('order_tracking.transport_cost_thai' , '>=', 0)
                                    ->where('order_tracking.import_fee' , '>=', 0)
                                    ->where('order_tracking.payment_status', false)
                                    ->get()->toArray();

                // print_r($tracking_no_list);exit;
                $track_arr = [];
                foreach ($tracking_no_list as $key => $value) {
                    $track_arr[] = $value['tracking_no'];
                }

                $message_desc = 'รายการฝากสั่ง ' . implode(',', $track_arr) . "\r\nขณะนี้อยู่ในสถานะ \"รอการชำระค่าขนส่ง\"\r\nกรุณาเข้าสู่ระบบเพื่อดำเนินการชำระค่าขนส่งสินค้า ขอบคุณค่ะ";
                // sendSms($mobile_no, $message);

                
            }

            $to_user_id = $order->customer->line_user_id;

            if(!empty($line_admin_remark)){
                $line_admin_remark = 'หมายเหตุเพิ่มเติม : ' . $line_admin_remark;
            }

            $line_admin_remark .= "\r\n\r\nhttps://cargomall.co.th";

            // $line_config = LineConfigItem::where('id', $line_config_item_id)->first();
            $send_data = [];
            $send_data['to'] = $to_user_id;
            $send_data['messages'] = [['type' => $message_type, 'text' => $message_desc . "\r\n\r\n" . $line_admin_remark]];

            $post_body = json_encode($send_data, JSON_UNESCAPED_UNICODE);

            $send_result = $this->send_line_message($API_URL, $POST_HEADER, $post_body);

            Log::info($send_result);
        }

        // if($send_result){
        // $this->saveLineMessage($message_type, $message_group, $message_desc, $line_user_account, $admin_id);    
        // }
        $this->data_result['DATA'] = $send_result;
        
        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    private function send_line_message($url, $post_header, $post_body)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
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
            $order->china_order_no = $Order['china_order_no'];
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

        foreach ($Order['order_trackings'] as $key => $value) {
            $order_tracking = OrderTracking::find($value['id']);
            if($order_tracking){
                $order_tracking->china_order_no = $value['china_order_no'];
                $order_tracking->china_tracking_no = $value['china_tracking_no'];
                $order_tracking->product_type = $value['product_type'];
                $order_tracking->transport_cost_china = $value['transport_cost_china'];
                $order_tracking->transport_cost_thai = $value['transport_cost_thai'];
                $order_tracking->tracking_no_thai = $value['tracking_no_thai'];
                $order_tracking->import_fee = $value['import_fee'];
                $order_tracking->save();
            }

        }

        $order_id = $order->id;
        $order_no = $order->order_no;
        $admin_id = $user_data['id'];
        $admin_name = $user_data['firstname'] . ' ' . $user_data['lastname'];

        addOrderActivityLog('update_order_detail', $order_id, $admin_id, $admin_name, $order_no, null, null, null, null);

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

            $order_id = $order->id;
            $order_no = $order->order_no;
            $admin_id = $user_data['id'];
            $admin_name = $user_data['firstname'] . ' ' . $user_data['lastname'];

            $last_order_status = $order->order_status;
            Log::info("Order No : " . $order->order_no);
            $order->order_status = 8;
            $order->last_order_status = $last_order_status;
            $order->save();

            addOrderActivityLog('cancel_order', $order_id, $admin_id, $admin_name, $order_no, null, null, null, null);

            $this->data_result['DATA'] = true;
        }

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function cancelCancelStatus(){

        $params = Request::all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $id = trim($params['obj']['id']);
        
        Log::info("CANCEL_CANCEL_STATUS_ORDER => ");
        Log::info("By User : " . $user_data['firstname'] . ' ' . $user_data['lastname']);

        $order = Order::find($id);

        if($order){

            $order_id = $order->id;
            $order_no = $order->order_no;
            $admin_id = $user_data['id'];
            $admin_name = $user_data['firstname'] . ' ' . $user_data['lastname'];

            $last_order_status = $order->order_status;
            Log::info("Order No : " . $order->order_no);
            $order->order_status = $order->last_order_status;
            $order->last_order_status = null;
            $order->save();

            addOrderActivityLog('cancel_cancel_order', $order_id, $admin_id, $admin_name, $order_no, null, null, null, null);

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

            // $tracking_no = $order->$tracking_no;
            $order_id = $order->id;
            $order_no = $order->order_no;
            $admin_id = $user_data['id'];
            $admin_name = $user_data['user_code'] . ' ' . $user_data['firstname'] . ' ' . $user_data['lastname'];
            $subject = 'บริษัทขนส่ง';
            addOrderActivityLog('cus_update_order_detail', $order_id, $admin_id, $admin_name, $order_no, null, null, null,  $subject);

            $this->data_result['DATA'] = true;
        }

        return $this->returnResponse(200, $this->data_result, response(), false);

    }
    
    public function deleteTrack(){
        
        $params = Request::all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $id = $params['obj']['id'];

        $result = OrderTracking::find($id);
        
        $tracking_no = $result->tracking_no;
        $order_id = $result->order_id;
        // $order_no = $result->order_no;
        $admin_id = $user_data['id'];
        $admin_name = $user_data['firstname'] . ' ' . $user_data['lastname'];

        $result->delete();

        addOrderActivityLog('delete_tracking', $order_id, $admin_id, $admin_name, null, null, null, $tracking_no);

        $this->data_result['DATA'] = $result;

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
