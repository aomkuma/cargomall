<?php

namespace App\Http\Controllers;

use Request;

use Mockery;

use App\CartSession;

use JoggApp\GoogleTranslate\GoogleTranslate;
use JoggApp\GoogleTranslate\GoogleTranslateClient;

class ProductsController extends Controller
{

	public function updateCartSession(){
		$params = Request::all();

		if(isset($params['user_session']['user_data'])){
	        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
	        $user_id = ''.$user_data['id'];
	        $cart_desc = $params['obj']['cart_desc'];

	        if($cart_desc != null){
		        $cart = CartSession::where('user_id', $user_id)->first();

		        if($cart){
		        	$cart->cart_desc = $cart_desc;
		        	$cart->save();

		        }else{
		        	$cart = new CartSession();
		        	$cart->user_id = $user_id;
		        	$cart->cart_desc = $cart_desc;
		        	$cart->created_by = $user_id;

		        	$cart->save();

		        }
		    }else{
		    	CartSession::where('user_id', $user_id)->delete();
		    }
		}else{
			$this->data_result['STATUS'] = 'ERROR';
			$this->data_result['DATA'] = 'Cannot fetch user data';
		}

        return $this->returnResponse(200, $this->data_result, response(), false);

	}

	public function getCartSession(){

		$params = Request::all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_id = ''.$user_data['id'];

        $cart = CartSession::where('user_id', $user_id)->first();

        if($cart){
	        $this->data_result['DATA'] = $cart->cart_desc;
		}
		else{
			$this->data_result['DATA'] = null;	
		}
        return $this->returnResponse(200, $this->data_result, response(), false);
	}

	private function translateWord($keyword){
		// return $keyword;
		$translateClient = new GoogleTranslateClient(['api_key' => 'AIzaSyCIQ0NJYS09Gp3lJdpR4gsmS9QQ5xPs_fU', 'default_target_translation' => 'th']);
		$trans = new GoogleTranslate($translateClient);
		$result = $trans->justTranslate($keyword);
		return trim($result);
    }

    public function getItem(){

    	$params = Request::all();
        $link_url = $params['obj']['link_url'];

    	$api_index = $this->findAPI($link_url);
		if($api_index=='0'){
			$product_result = $this->callAPITaobao($link_url);
			$this->data_result['DATA'] = $product_result;
		}else if($api_index=='1'){
			$product_result = $this->callAPITmall($link_url);
			$this->data_result['DATA'] = $product_result;
		}else if($api_index=='2'){
			$product_result = $this->callAPI1688($link_url);
			$this->data_result['DATA'] = $product_result;
		}else{
			$this->data_result['STATUS'] = 'ERROR'; 
			$this->data_result['DATA'] = 'ไม่พบข้อมูลสินค้า กรุณาตรวจสอบ URL ให้ถูกต้อง';
		}
    	
		if(isset($this->data_result['DATA']['STATUS'])){
			$this->data_result['STATUS'] = 'ERROR'; 
			$this->data_result['DATA'] = $this->data_result['DATA']['MSG'];
		}

		return $this->returnResponse(200, $this->data_result, response(), false);

    }

    private function findAPI($url){
    	$website_list = array('taobao','tmall','1688');
		foreach($website_list as $key=>$val){
			if(strpos($url, $val) !=''){
				return $key;
			}
		}
    }

    private function callAPITaobao($link_url){
    	$url_arr = explode('.htm', $link_url);
		$itemId = substr($url_arr[0], strpos($url_arr[0], 'item/') + 5);
		if(!is_numeric($itemId)){
			$result = parse_url($link_url);
			parse_str($result['query'], $queryArray);
			$itemId = $queryArray['id'];
		}

			define('CFG_SERVICE_INSTANCEKEY', '52b832ea-3eb7-40fb-a66b-dc0114876a94'/*'opendemo'*/);
			define('CFG_REQUEST_LANGUAGE', 'en');
			 
			//$itemId = (isset($_REQUEST['itemId'])) ? $_REQUEST['itemId'] : 38237454486;
			 
			$api_url = 'http://otapi.net/OtapiWebService2.asmx/BatchGetItemFullInfo?instanceKey=' . CFG_SERVICE_INSTANCEKEY
			        . '&language=' . CFG_REQUEST_LANGUAGE
			        . '&itemId=' . $itemId
			        . '&sessionId=&blockList=';
			 
			$curl = curl_init();
			
			curl_setopt($curl, CURLOPT_URL, $api_url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			
			$result = curl_exec($curl);
			if ($result === FALSE) {
				$RETURN_DATA['STATUS'] = 'ERROR';
				$RETURN_DATA['MSG'] = 'Service error: ' . curl_error($curl);
				return $RETURN_DATA;
			    // echo json_encode($RETURN_DATA);die();
			}
			$xmlObject = simplexml_load_string($result);
			 
			curl_close($curl);
			 
			if ((string)$xmlObject->ErrorCode !== 'Ok') {
				$RETURN_DATA['STATUS'] = 'ERROR';
				$RETURN_DATA['MSG'] = "Error: " . $xmlObject->ErrorDescription;
				return $RETURN_DATA;
			    // echo json_encode($RETURN_DATA);die();
			}
			 
			$itemInfo = $xmlObject->Result->Item;
			
			// print_r($itemInfo);
			// exit;

			$ProductLevelList = [];
			foreach ($itemInfo->Attributes->ItemAttribute as $key => $value) {
				// $quantity = simpleXmlToArray($value)['Quantity'];
				// $price = simpleXmlToArray($value->Price)['OriginalPrice'];
				// $description_arr = $value->Configurators;
				
				// echo $description;
				// $detail = ['quantity' => $quantity, 'price' => $price, 'description' => $description];
				// $ProductLevelList[] = $detail;
				$description = '';
				if($value->PropertyName == 'specification' || strpos(strtolower($value->PropertyName), 'size') !== false){

					$description = ((array) $value->Value)[0];

					$translate_description = $this->translateWord($description);
					if(!empty($translate_description)){
						$description = $translate_description;
					}

					$ProductLevelList[] = ['vid' => (string) $value->Attributes()->Vid, 'description' => $description, 'quantity' => 0, 'price' => 0];
				}
				// print_r(($value));
				
			}

			// print_r($ProductLevelList);
			// exit;

			// $cnt = 0;
			for($i = 0; $i < count($ProductLevelList); $i++){

				foreach ($itemInfo->ConfiguredItems->OtapiConfiguredItem as $key => $value) {
					$quantity = simpleXmlToArray($value)['Quantity'];
					$price = simpleXmlToArray($value->Price)['OriginalPrice'];
					$description_arr = $value->Configurators;
					
					$vid = '';
					// $detail_arr = [];
					foreach ($description_arr->ValuedConfigurator as $desc_key => $desc_value) {
						$vid = (string) $desc_value['Vid'];
					}

					if($vid == $ProductLevelList[$i]['vid'] && $price > $ProductLevelList[$i]['price']){
						// echo $description;
						$ProductLevelList[$i]['price_vid'] = $vid;
						$ProductLevelList[$i]['quantity'] = $quantity; 
						$ProductLevelList[$i]['price'] = $price;
						// $cnt++;
					}
				}

			}

			// print_r($ProductLevelList);
			// exit;
			$itemAttributes = array();
			$arr_color_img = array();
			$arr_color = array();
			$arr_color_check = array();
			$arr_size = array();
			if (isset($itemInfo->Attributes->ItemAttribute)) {
				// $cnt_color_img = 0;
			    foreach ($itemInfo->Attributes->ItemAttribute as $ItemAttribute) {
			       	// echo $ItemAttribute->PropertyName;
			       	$vid = null;
					if(strtolower(trim($ItemAttribute->PropertyName)) == 'colour' || strtolower(trim($ItemAttribute->PropertyName)) == 'color classification' || strtolower(trim($ItemAttribute->PropertyName)) == 'primary color' || strtolower(trim($ItemAttribute->PropertyName)) == 'food taste'){
					 	$color_val = (string)$ItemAttribute->Value;
					 	if(isset($ItemAttribute->ImageUrl)){
					 		$arr_color_img[] = (string)$ItemAttribute->ImageUrl; 
					 		// $arr_color_img[$cnt_color_img]['name'] = (string)$ItemAttribute->ValueAlias;
					 		// $cnt_color_img++;
					 	}else if(isset($ItemAttribute->ValueAlias)){
					 		$arr_color_img[] = (string)$ItemAttribute->ValueAlias; 
					 		// $arr_color_img[$cnt_color_img]['name'] = (string)$ItemAttribute->ValueAlias;
					 		// $cnt_color_img++;
					 	}
						if(/*isset($ItemAttribute->ValueAlias) && */((string)$ItemAttribute->ValueAlias) != ''){
							$color_val = (string)$ItemAttribute->ValueAlias;
							$vid = (string)$ItemAttribute->Attributes()->Vid;
							// print_r($ItemAttribute);
						}else{
							$color_val = (string)$ItemAttribute->Value;
							$vid = (string)$ItemAttribute->Attributes()->Vid;
						}

						$arr_color[] = $color_val;

						$res = [];
						$res['name'] = $color_val;
						$res['vid'] = '';
						if($vid !== null){
							$res['vid'] = $vid;
						}
						$arr_color_check[] = $res;
					 }
					 
					 else if(strtolower($ItemAttribute->PropertyName) == 'size'){
					 	$arr_size[] = (string)$ItemAttribute->Value;
					 }
			    }
			}
			
			$price_list_by_color = array();
			// print_r($itemInfo->ConfiguredItems);
			// exit;
			if(!empty($arr_color_check)){

				foreach ($arr_color_check as $key => $value) {
					
					foreach ($itemInfo->ConfiguredItems->OtapiConfiguredItem as $OtapiConfiguredItem) {

						$price_vid = (string)$OtapiConfiguredItem->Configurators->ValuedConfigurator->Attributes()->Vid;
						if($value['vid'] == $price_vid){
							// echo  . "<br>";

							// $res = [];
							// $res['name'] = $value['name'];
							// $res['price'] = (string)$OtapiConfiguredItem->Price->OriginalPrice;
							// $res['vid'] = $vid;
							// $price_list_by_color[] = $res;

							$price_list_by_color[] = (string)$OtapiConfiguredItem->Price->OriginalPrice;
						}
					}

				}

			}
			
			// echo '<pre>';
			// echo '<br><br>';
			// $ProductLevelList = [];
			$product_color_choose = '';
			if(empty($arr_color_img) && !empty($arr_color)){
				$product_color_choose = $arr_color[0];
			}
			$price_range_list = [];
			
			$product_result = array('product_url'=>(string)$itemInfo->ExternalItemUrl
									,'product_original_name'=>(string)$itemInfo->Title
									,'product_image'=>(string)$itemInfo->MainPictureUrl
									,'product_color_img'=>$arr_color_img
									,'product_color'=>$arr_color
									,'product_size'=>$arr_size
									,'product_qty'=>1
									,'product_normal_price'=>floatval($itemInfo->Price->OriginalPrice)
									,'product_currency_displayname'=>'¥'
									,'product_promotion_price'=>0
									,'merchant_name'=>$itemInfo->VendorName
									,'product_color_img_choose'=>empty($arr_color_img)?[]:$arr_color_img[0]
									,'product_color_choose'=>$product_color_choose
									,'product_size_choose'=>empty($arr_size)?[]:$arr_size[0]
									,'price_list_by_color'=>$price_list_by_color
									,'ProductLevelList' => $ProductLevelList
									,'PriceRangeList' => $price_range_list
								);

			return $product_result;
    }

    private function callAPITmall($link_url){
    	$url_arr = explode('.htm', $link_url);
		$itemId = substr($url_arr[0], strpos($url_arr[0], 'item/') + 5);
		if(!is_numeric($itemId)){
			$result = parse_url($link_url);
			parse_str($result['query'], $queryArray);
			$itemId = $queryArray['id'];
		}

			define('CFG_SERVICE_INSTANCEKEY', 'opendemo');
			define('CFG_REQUEST_LANGUAGE', 'en');
			 
			//$itemId = (isset($_REQUEST['itemId'])) ? $_REQUEST['itemId'] : 38237454486;
			 
			$api_url = 'http://otapi.net/OtapiWebService2.asmx/BatchGetItemFullInfo?instanceKey=' . CFG_SERVICE_INSTANCEKEY
			        . '&language=' . CFG_REQUEST_LANGUAGE
			        . '&itemId=' . $itemId
			        . '&sessionId=&blockList=';
			 
			$curl = curl_init();
			
			curl_setopt($curl, CURLOPT_URL, $api_url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HEADER, 0);
			
			$result = curl_exec($curl);
			if ($result === FALSE) {
				$RETURN_DATA['STATUS'] = 'ERROR';
				$RETURN_DATA['MSG'] = 'Service error: ' . curl_error($curl);
				return $RETURN_DATA;
			    // echo json_encode($RETURN_DATA);die();
			}
			$xmlObject = simplexml_load_string($result);
			 
			curl_close($curl);
			 
			if ((string)$xmlObject->ErrorCode !== 'Ok') {
				$RETURN_DATA['STATUS'] = 'ERROR';
				$RETURN_DATA['MSG'] = "Error: " . $xmlObject->ErrorDescription;
				return $RETURN_DATA;
			    // echo json_encode($RETURN_DATA);die();
			}
			 
			$itemInfo = $xmlObject->Result->Item;
			
			// print_r($itemInfo);exit;
			$itemAttributes = array();
			$arr_color_img = array();
			$arr_color = array();
			$arr_color_check = array();
			$arr_size = array();
			if (isset($itemInfo->Attributes->ItemAttribute)) {
			    foreach ($itemInfo->Attributes->ItemAttribute as $ItemAttribute) {
			       
					if(strtolower(trim($ItemAttribute->PropertyName)) == 'colour' || strtolower(trim($ItemAttribute->PropertyName)) == 'color classification' || strtolower(trim($ItemAttribute->PropertyName)) == 'primary color'){
					 	$color_val = (string)$ItemAttribute->Value;
					 	if(isset($ItemAttribute->ImageUrl)){
					 		$arr_color_img[] = (string)$ItemAttribute->ImageUrl; 
					 	}
						if(isset($ItemAttribute->ValueAlias) && trim($ItemAttribute->ValueAlias) != ''){
							$color_val = (string)$ItemAttribute->ValueAlias;
							$vid = (string)$ItemAttribute->Attributes()->Vid;
							// print_r($ItemAttribute);
						}

						$arr_color[] = $color_val;

						$res = [];
						$res['name'] = $color_val;
						$res['vid'] = $vid;

						$arr_color_check[] = $res;
					 }
					 
					 else if(strtolower($ItemAttribute->PropertyName) == 'size'){
					 	$arr_size[] = (string)$ItemAttribute->Value;
					 }
			    }
			}
			
			$price_list_by_color = array();
			// print_r($itemInfo->ConfiguredItems);
			// exit;
			if(!empty($arr_color_check)){

				foreach ($arr_color_check as $key => $value) {
					
					foreach ($itemInfo->ConfiguredItems->OtapiConfiguredItem as $OtapiConfiguredItem) {

						$price_vid = (string)$OtapiConfiguredItem->Configurators->ValuedConfigurator->Attributes()->Vid;
						if($value['vid'] == $price_vid){
							// echo  . "<br>";

							// $res = [];
							// $res['name'] = $value['name'];
							// $res['price'] = (string)$OtapiConfiguredItem->Price->OriginalPrice;
							// $res['vid'] = $vid;
							// $price_list_by_color[] = $res;

							$price_list_by_color[] = (string)$OtapiConfiguredItem->Price->OriginalPrice;
						}
					}

				}

			}

			// print_r($price_list_by_color);
			// exit;
			// echo '<pre>';
			// echo '<br><br>';
			$ProductLevelList = [];
			$price_range_list = [];
			$product_result = array('product_url'=>(string)$itemInfo->ExternalItemUrl
									,'product_original_name'=>(string)$itemInfo->Title
									,'product_image'=>(string)$itemInfo->MainPictureUrl
									,'product_color_img'=>$arr_color_img
									,'product_color'=>$arr_color
									,'product_size'=>$arr_size
									,'product_qty'=>1
									,'product_normal_price'=>floatval($itemInfo->Price->OriginalPrice)
									,'product_currency_displayname'=>'¥'
									,'product_promotion_price'=>0
									,'merchant_name'=>$itemInfo->VendorName
									,'product_color_img_choose'=>empty($arr_color_img)?[]:$arr_color_img[0]
									,'product_color_choose'=>trim(empty($arr_color)?'':$arr_color[0])
									,'product_size_choose'=>empty($arr_size)?[]:$arr_size[0]
									,'price_list_by_color'=>$price_list_by_color
									,'ProductLevelList' => $ProductLevelList
									,'PriceRangeList' => $price_range_list
								);

			return $product_result;
    }

    public function callAPI1688($link_url){
    	$url_arr = explode('.html', $link_url);
		$itemId = 'abb-'. substr($url_arr[0], strpos($url_arr[0], 'offer/') + 6);

		define('CFG_SERVICE_INSTANCEKEY', '52b832ea-3eb7-40fb-a66b-dc0114876a94'/*'opendemo'*/);
		define('CFG_REQUEST_LANGUAGE', 'en');
			
		$api_url = 'http://otapi.net/OtapiWebService2.asmx/BatchGetItemFullInfo?instanceKey=' . CFG_SERVICE_INSTANCEKEY
			        . '&language=' . CFG_REQUEST_LANGUAGE
			        . '&itemId=' . $itemId
			        . '&sessionId=&blockList=';
			 
		$curl = curl_init();
		
		curl_setopt($curl, CURLOPT_URL, $api_url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		
		$result = curl_exec($curl);
		if ($result === FALSE) {
			$RETURN_DATA['STATUS'] = 'ERROR';
			$RETURN_DATA['MSG'] = 'Service error: ' . curl_error($curl);
			return $RETURN_DATA;
		    // echo json_encode($RETURN_DATA);die();
		}
		$xmlObject = simplexml_load_string($result);
		 
		curl_close($curl);
		 
		if ((string)$xmlObject->ErrorCode !== 'Ok') {
			$RETURN_DATA['STATUS'] = 'ERROR';
			$RETURN_DATA['MSG'] = "Error: " . $xmlObject->ErrorDescription;
			return $RETURN_DATA;
		    // echo json_encode($RETURN_DATA);die();
		}
		 
		$itemInfo = $xmlObject->Result->Item;
		// print_r($itemInfo);exit;
		$price_range_list = [];
		$cnt = 0;

		if($itemInfo->QuantityRanges->Range){
			foreach ($itemInfo->QuantityRanges->Range as $key => $value) {
				// print_r($value);

				if($cnt > 0){
					$price_range_list[$cnt - 1]['max_qty'] = (string) $value->MinQuantity;
				}

				$price = [
							'min_qty' => (string) $value->MinQuantity,
							'max_qty' => -1,
							'price' => (string) $value->Price->OriginalPrice,
						];

				$price_range_list[$cnt] = $price;

				$cnt++;
			}
		}

		// print_r($price_range_list);

		// exit;

		$ProductLevelList = [];
		foreach ($itemInfo->ConfiguredItems->OtapiConfiguredItem as $key => $value) {
			$quantity = simpleXmlToArray($value)['Quantity'];
			$price = simpleXmlToArray($value->Price)['OriginalPrice'];
			$description_arr = $value->Configurators;
			// print_r($description_arr);
			// $description = implode(' ', (array)$description_arr->Vid);
			$description = '';
			$detail_arr = [];
			foreach ($description_arr->ValuedConfigurator as $desc_key => $desc_value) {
				$detail_arr[]= $desc_value['Vid'];
			}

			$description = implode(' ', $detail_arr);
			$translate_description = $this->translateWord($description);
			if(!empty($translate_description)){
				$description = $translate_description;
			}

			// echo $description;
			$detail = ['quantity' => $quantity, 'price' => $price, 'description' => $description];
			$ProductLevelList[] = $detail;
			// print_r(($value));
			// print_r(simpleXmlToArray($value->Price));
			// print_r($value->Price->OriginalPrice[0]);
			// print_r(simpleXmlToArray($value));
		}
		// $collection = collect($ProductLevelList);
		// asort($ProductLevelList);
		// $ProductLevelList = collect($collection)->sortBy('price')->reverse()->toArray();
		// print_r($itemInfo->ConfiguredItems->OtapiConfiguredItem);
		// exit;
		$itemAttributes = array();
		$arr_color_img = array();
		$arr_color = array();
		$arr_size = array();
		if (isset($itemInfo->Attributes->ItemAttribute)) {
		    foreach ($itemInfo->Attributes->ItemAttribute as $ItemAttribute) {
		       
				if(strtolower(trim($ItemAttribute->PropertyName)) == 'colour' || strtolower(trim($ItemAttribute->PropertyName)) == 'color classification' || strtolower(trim($ItemAttribute->PropertyName)) == 'primary color' || strtolower(trim($ItemAttribute->PropertyName)) == 'model'){
				 	$color_val = (string)$ItemAttribute->Value;
				 	if(isset($ItemAttribute->ImageUrl)){
				 		$arr_color_img[] = (string)$ItemAttribute->ImageUrl; 
				 	}
					if(isset($ItemAttribute->ValueAlias) && trim($ItemAttribute->ValueAlias) != ''){
						$color_val = (string)$ItemAttribute->ValueAlias;
					}
					$arr_color[] = $color_val;
				 }
				 
				 else if(strtolower($ItemAttribute->PropertyName) == 'size'){
				 	$arr_size[] = (string)$ItemAttribute->Value;
				 }
		    }
		}
		
		$price_list_by_color = array();
		foreach ($itemInfo->ConfiguredItems->OtapiConfiguredItem as $OtapiConfiguredItem) {
			$price_list_by_color[] = (string)$OtapiConfiguredItem->Price->OriginalPrice;
		}
		
		// echo '<pre>';
		// echo '<br><br>';
		
		$product_result = array('product_url'=>(string)$itemInfo->ExternalItemUrl
								,'product_original_name'=>(string)$itemInfo->Title
								,'product_image'=>(string)$itemInfo->MainPictureUrl
								,'product_color_img'=>$arr_color_img
								,'product_color'=>$arr_color
								,'product_size'=>$arr_size
								,'product_qty'=>1
								,'product_normal_price'=>floatval($itemInfo->Price->OriginalPrice)
								,'product_currency_displayname'=>'¥'
								,'product_promotion_price'=>0
								,'merchant_name'=>$itemInfo->VendorName
								,'product_color_img_choose'=>empty($arr_color_img)?[]:$arr_color_img[0]
								,'product_color_choose'=>trim(empty($arr_color)?'':$arr_color[0])
								,'product_size_choose'=>empty($arr_size)?[]:$arr_size[0]
								,'price_list_by_color'=>$price_list_by_color
								,'ProductLevelList' => $ProductLevelList
								,'PriceRangeList' => $price_range_list
							);

		return $product_result;
    }
}
