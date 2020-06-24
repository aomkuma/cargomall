<?php

	use App\User;
	use App\MoneyBag;
	use App\ExchangeRate;
	use App\ExchangeRateTransfer;

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
        $params['username'] = env('SMS_USERNAME');
        $params['password'] = env('SMS_PASSWORD');
 
        $params['from']     = '0000';
        $params['to']       = $to;
        $params['message']  = $message;
 
        if (is_null( $params['to']) || is_null( $params['message']))
        {
            return FALSE;
        }
 
        $result = curl( $params);
        $xml = @simplexml_load_string( $result);
        if (!is_object($xml))
        {
            return array( FALSE, 'Respond error');
        } else {
            if ($xml->send->status == 'success')
            {
                return array( TRUE, $xml->send->uuid);
            } else {
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