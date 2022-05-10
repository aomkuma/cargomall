

<?php

	use App\User;
	use App\MoneyBag;
	use App\ExchangeRate;
	use App\ExchangeRateTransfer;
	use App\Models\OrderActivityLog;

	function generateID(){
		return rand(1000000000,8999999999) . date('Ym');
	}

	function generateOrderNo($max_running){
		// $rand_1 = rand(0, 999);
		// $rand_1 = str_pad($rand_1, 3,'0', STR_PAD_LEFT);
		// $rand_2 = rand(0, 99);
		// $rand_2 = str_pad($rand_1, 2,'0', STR_PAD_LEFT);
		// return 'CGM' . $rand_1 . date('y') . $rand_2 . date('md');
		$running =  str_pad($max_running, 6,'0', STR_PAD_LEFT);
		return 'CGM' . date('Ymd') . $running;
	}

	function getDateFromString($d){

		if(empty($d)){
			return null;
		}else{
			return date('Y-m-d', strtotime($d));
		}
		
	}

	function getDateTimeFromString($d){
		if(empty($d)){
			return null;
		}else{
			return date('Y-m-d H:i:s', strtotime($d));
		}
	}

	function getUserProfile($user_id){
		return User::with(['addresses' => function($q){
                        $q->orderBy('address_no', 'ASC');
                    }])
					->with('moneyBags')
                    ->where('id', $user_id)
                    ->first();
	}

	function checkAccountBalance($user_id){
		$money_bag = MoneyBag::where('user_id', $user_id)->first();
		return $money_bag->balance;
	}

	function getLastChinaRate(){
		$exchange_rate = ExchangeRate::orderBy('created_at', 'DESC')->first();
		return ['exchange_rate'=> $exchange_rate->exchange_rate, 'last_update_exrate' => $exchange_rate->created_at->toDateTimeString()];
	}

	function getLastChinaRateTransfer(){
		$exchange_rate = ExchangeRateTransfer::orderBy('created_at', 'DESC')->first();
		return $exchange_rate->exchange_rate;
	}

	function sendSms($to=null, $message=null)
    {
        // $to = '0849966989';
        // $message = 'ทดสอบส่งข้อความจาก Cargo Mall';   

        $params['method']   = 'send';
        $params['username'] = env('SMS_USERNAME', 'juneugen');
        $params['password'] = env('SMS_PASSWORD', '0c7c6f');
 
        $params['from']     = 'Cargo Mall';
        $params['to']       = $to;
        $params['message']  = $message;
 
        if (is_null( $params['to']) || is_null( $params['message']))
        {
            return FALSE;
        }
 
        $result = curl( $params);
        $xml = @simplexml_load_string( $result);
        \Log::info($result);
        if (!is_object($xml))
        {
            \Log::error('SMS to ' .$to. ' ERROR : ' . $xml->send->send->uuid);
            return array( FALSE, 'Respond error');
        } else {
            if ($xml->send->status == 'success')
            {
            	\Log::info('SMS to ' .$to. ' SUCCESS : ' . $xml->send->send->uuid);
                return array( TRUE, $xml->send->uuid);
            } else {
            	\Log::error('SMS to ' .$to. ' ERROR : ' . $xml->send->message);
                return array( FALSE, $xml->send->message);
            }
        }
    }
     
    function curl( $params=array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://www.thsms.com/api/rest');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( $params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 
        $response  = curl_exec($ch);
        $lastError = curl_error($ch);
        $lastReq = curl_getinfo($ch);
        curl_close($ch);
 
        return $response;
    }

    function simpleXmlToArray($xmlObject)
	{
	    $array = [];
	    foreach ($xmlObject->children() as $node) {
	        $array[$node->getName()] = is_array($node) ? simplexml_to_array($node) : (string) $node;
	    }

	    return $array;
	}

	function addOrderActivityLog($log_type, $order_id, $admin_id, $admin_name = null,$order_no = null, $old_status = null, $new_status = null, $tracking_no = null, $subject = null){

		$data = [];
		$data['log_type'] = $log_type;
		$data['order_id'] = $order_id;
		$data['description'] = orderActivityLogTemplate($log_type, $admin_name, $order_no, $old_status, $new_status, $tracking_no, $subject);
		$data['admin_id'] = $admin_id;
		$data['admin_name'] = $admin_name;

		OrderActivityLog::create($data);
	}

	function orderActivityLogTemplate($log_type, $admin_name,$order_no = null, $old_status = null, $new_status = null, $tracking_no = null, $subject = null){
		switch ($log_type) {
			case 'cus_update_order_detail': $description = 'ลูกค้า : ' . $admin_name . ' แก้ไขข้อมูลรายละเอียดเลขที่ใบสั่งซื้อ ' . $order_no . ' เรื่อง ' . $subject;break;
			case 'update_order_detail': $description = $admin_name . ' แก้ไขข้อมูลรายละเอียด' . 
					(($subject != null)?' เรื่อง ' . $subject:'');break;
			case 'update_product': $description = $admin_name . ' แก้ไขข้อมูลจำนวน, ราคาของสินค้า';break;
			case 'cancel_order': $description = $admin_name . ' ทำการยกเลิกเลขที่ใบสั่งซื้อ ' . $order_no;break;
			case 'cancel_cancel_order': $description = $admin_name . ' ทำการยกเลิก การยกเลิกเลขที่ใบสั่งซื้อ ' . $order_no;break;
			case 'update_status': $description = $admin_name . ' แก้ไขสถานะจาก ' . getOrderStatusName($old_status) . ' เป็น ' . getOrderStatusName($new_status);break;
			case 'delete_tracking' : $description = $admin_name . ' ลบข้อมูลรายการแทรคเลข ' . $tracking_no;break;
			default: break;
		}

		return $description;
	}

	function getOrderStatusName($status){

		switch ($status) {
			case 0: return 'ตรวจสอบคำสั่งซื้อ';break;
			case 1: return 'รอการชำระเงินค่าสินค้า';break;
			case 2: return 'ชำระเงินค่าสินค้าแล้ว';break;
			case 3: return 'ดำเนินการสั่งซื้อสินค้า';break;
			case 4: return 'สินค้าออกจากโกดังจีน';break;
			case 5: return 'สินค้าถึงโกดังไทย';break;
			case 6: return 'อยู่ระหว่างกระบวนการจัดส่ง';break;
			case 7: return 'เสร็จสิ้น';break;
			case 8: return 'ยกเลิก';break;
			default:break;
		}
		
	}

	function execInForeground($cmd) { 
        if (substr(php_uname(), 0, 7) == "Windows"){ 
            pclose(popen("start ". $cmd, "r"));  
        } 
        else { 
            exec($cmd . " > /dev/null &");   
        } 
    }

    function execInBackground($cmd) { 
        if (substr(php_uname(), 0, 7) == "Windows"){ 
            pclose(popen("start /B ". $cmd, "r"));  
        } 
        else { 
            exec($cmd . " > /dev/null &");   
        } 
    }
