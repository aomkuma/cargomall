<?php

namespace App\Http\Controllers;

use Request;

use Mockery;

use App\CartSession;

use JoggApp\GoogleTranslate\GoogleTranslate;
use JoggApp\GoogleTranslate\GoogleTranslateClient;

// use Stichoza\GoogleTranslate\GoogleTranslate;

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
		$keyword = trim($keyword);
		$excep_keyword = ['L', 'Y', 'Z'];
		if(in_array(strtoupper($keyword), $excep_keyword)){
			return $keyword;
		}

		$translateClient = new GoogleTranslateClient(['api_key' => 'AIzaSyCNdgYB9ssHXpWCEWXTy8xwXsTwq7r8SX4', 'default_target_translation' => 'en']);
		$trans = new GoogleTranslate($translateClient);
		$result = $trans->justTranslate(strtoupper($keyword), 'en');

		// $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
		// $tr->setSource('en'); // Translate from English
		// $tr->setSource('zh'); // Detect language automatically
		// $tr->setTarget('en');
		// $result = $tr->translate($keyword);
		// \Log::info($keyword . ' : translate to : ' . $result);
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
				$RETURN_DATA['MSG'] = 'API Service Error 1 : ' . curl_error($curl);
				return $RETURN_DATA;
			    // echo json_encode($RETURN_DATA);die();
			}
			$xmlObject = simplexml_load_string($result);
			 
			curl_close($curl);
			 
			if ((string)$xmlObject->ErrorCode !== 'Ok') {
				$RETURN_DATA['STATUS'] = 'ERROR';
				$RETURN_DATA['MSG'] = "API Service Error 1 : " . $xmlObject->ErrorDescription;
				return $RETURN_DATA;
			    // echo json_encode($RETURN_DATA);die();
			}
			 
			$itemInfo = $xmlObject->Result->Item;

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
			
			// \Log::info(print_r($itemInfo, true));
			// exit;
			$ProductLevelList = [];
			foreach ($itemInfo->Attributes->ItemAttribute as $key => $value) {
				$description = '';
				if($value->PropertyName == 'specification' || strpos(strtolower($value->PropertyName), 'size') !== false){

					$description = ((array) $value->Value)[0];
					$ProductLevelList[] = ['vid' => (string) $value->Attributes()->Vid, 'description' => $description, 'quantity' => 0, 'price' => 0];
				}
				// print_r(($value));
				
			}

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

			       	$vid = null;
					// if(strtolower(trim($ItemAttribute->PropertyName)) == 'colour' || strtolower(trim($ItemAttribute->PropertyName)) == 'color classification' || strtolower(trim($ItemAttribute->PropertyName)) == 'primary color' || strtolower(trim($ItemAttribute->PropertyName)) == 'food taste' || strtolower(trim($ItemAttribute->PropertyName)) == 'taste' || strtolower(trim($ItemAttribute->PropertyName)) == 'net weight' || strtolower(trim($ItemAttribute->PropertyName)) == 'sort by color'){

			       	// if(isset($ItemAttribute->PropertyName) && strpos(strtolower($ItemAttribute->PropertyName), 'size') == false){

			       	if(isset($ItemAttribute->IsConfigurator) && $ItemAttribute->IsConfigurator == 'true' && strpos(strtolower($ItemAttribute->PropertyName), 'size') === false){

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
					 
					 else if(isset($ItemAttribute->IsConfigurator) && $ItemAttribute->IsConfigurator == 'true') /*if(strtolower($ItemAttribute->PropertyName) == 'size')*/{
					 	$arr_size[] = (string)$ItemAttribute->Value;
					 }
			    }
			}
			
			$price_list_by_color = array();
			$price_range_list = [];
			// print_r($itemInfo->ConfiguredItems);
			// exit;
			if(!empty($arr_color_check)){

				foreach ($arr_color_check as $key => $value) {
					
					foreach ($itemInfo->ConfiguredItems->OtapiConfiguredItem as $OtapiConfiguredItem) {

						$price_vid = (string)$OtapiConfiguredItem->Configurators->ValuedConfigurator->Attributes()->Vid;
						if($value['vid'] == $price_vid && !in_array((string)$OtapiConfiguredItem->Price->OriginalPrice, $price_list_by_color)){

							$price_list_by_color[] = (string)$OtapiConfiguredItem->Price->OriginalPrice;
							$price_range_list[] = (string)$OtapiConfiguredItem->Price->OriginalPrice;
						}else{

							$price_vid = (string)$OtapiConfiguredItem->Configurators->ValuedConfigurator->Attributes()->Id;
							if($value['vid'] == $price_vid && !in_array((string)$OtapiConfiguredItem->Price->OriginalPrice, $price_list_by_color)){
								
								$price_list_by_color[] = (string)$OtapiConfiguredItem->Price->OriginalPrice;
								$price_range_list[] = (string)$OtapiConfiguredItem->Price->OriginalPrice;
							}

						}
					}

				}

			}
			
			$product_color_choose = '';
			if(empty($arr_color_img) && !empty($arr_color)){
				$product_color_choose = $arr_color[0];
			}

			

			if(count($price_range_list) > 3){

				usort($price_range_list, function($a, $b) {
					if($a > $b){
						return $a;	
					}
				    
				});

				$price_range_list = [$price_range_list[0], $price_range_list[count($price_range_list) - 1]];
			}

			$IsHasItems = true;
			
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
									,'IsHasItems'=>$IsHasItems
									,'ProductLevelList' => $ProductLevelList
									,'PriceRangeList' => $price_range_list
									,'exchange_rate' =>getLastChinaRate()['exchange_rate']
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

			define('CFG_SERVICE_INSTANCEKEY', '52b832ea-3eb7-40fb-a66b-dc0114876a94');
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
			// print_r($xmlObject);exit;
			curl_close($curl);
			 
			if ((string)$xmlObject->ErrorCode !== 'Ok') {
				$RETURN_DATA['STATUS'] = 'ERROR';
				$RETURN_DATA['MSG'] = "Error: " . $xmlObject->ErrorDescription;
				return $RETURN_DATA;
			    // echo json_encode($RETURN_DATA);die();
			}
			 
			$itemInfo = $xmlObject->Result->Item;
			// \Log::info(print_r($itemInfo, true));
			// print_r($itemInfo);exit;
			$ProductLevelList = [];
			$price_range_list = [];
			$itemAttributes = array();
			$arr_color_img = array();
			$arr_color = array();
			$arr_color_check = array();
			$arr_size = array();

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

			$ProductLevelList = [];
			foreach ($itemInfo->Attributes->ItemAttribute as $key => $value) {
				$description = '';
				if($value->PropertyName == 'specification' || strpos(strtolower($value->PropertyName), 'size') !== false){

					$description = ((array) $value->Value)[0];
					$ProductLevelList[] = ['vid' => (string) $value->Attributes()->Vid, 'description' => $description, 'quantity' => 0, 'price' => 0];
				}
				// print_r(($value));
				
			}

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

			if (isset($itemInfo->Attributes->ItemAttribute)) {
			    foreach ($itemInfo->Attributes->ItemAttribute as $ItemAttribute) {
			       
					//if(strtolower(trim($ItemAttribute->PropertyName)) == 'colour' || strtolower(trim($ItemAttribute->PropertyName)) == 'color classification' || strtolower(trim($ItemAttribute->PropertyName)) == 'primary color' || strtolower(trim($ItemAttribute->PropertyName)) == 'taste' || strtolower(trim($ItemAttribute->PropertyName)) == 'net weight' || strtolower(trim($ItemAttribute->PropertyName)) == 'sort by color'){

			    	// if(isset($ItemAttribute->PropertyName) && strpos(strtolower($ItemAttribute->PropertyName), 'size') == false){

			    	if(isset($ItemAttribute->IsConfigurator) && $ItemAttribute->IsConfigurator == 'true' && strpos(strtolower($ItemAttribute->PropertyName), 'size') === false){
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
						if(isset($vid)){
							$res['vid'] = $vid;
						}

						$arr_color_check[] = $res;
					 }
					 
					 else if(isset($ItemAttribute->IsConfigurator) && $ItemAttribute->IsConfigurator == 'true') /*if(strtolower($ItemAttribute->PropertyName) == 'size')*/{
					 	$arr_size[] = (string)$ItemAttribute->Value;
					 }
			    }
			}
			
			$price_list_by_color = array();
			if(!empty($arr_color_check)){

				foreach ($arr_color_check as $key => $value) {
					
					foreach ($itemInfo->ConfiguredItems->OtapiConfiguredItem as $OtapiConfiguredItem) {

						$price_vid = (string)$OtapiConfiguredItem->Configurators->ValuedConfigurator->Attributes()->Vid;
						if(isset($value['vid']) && $value['vid'] == $price_vid){
							
							$price_list_by_color[] = (string)$OtapiConfiguredItem->Price->OriginalPrice;
							$price_range_list[] = (string)$OtapiConfiguredItem->Price->OriginalPrice;
						}else{

							$price_vid = (string)$OtapiConfiguredItem->Configurators->ValuedConfigurator->Attributes()->Id;
							if(isset($value['vid']) && $value['vid'] == $price_vid){

								$price_list_by_color[] = (string)$OtapiConfiguredItem->Price->OriginalPrice;
								$price_range_list[] = (string)$OtapiConfiguredItem->Price->OriginalPrice;
							}

						}
					}

				}

			}

			$IsHasItems = true;

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
									,'IsHasItems'=>$IsHasItems
									,'ProductLevelList' => $ProductLevelList
									,'PriceRangeList' => $price_range_list
									,'exchange_rate' =>getLastChinaRate()['exchange_rate']
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
			$RETURN_DATA['MSG'] = 'API Service Error 1 : ' . curl_error($curl);
			return $RETURN_DATA;
		    // echo json_encode($RETURN_DATA);die();
		}
		$xmlObject = simplexml_load_string($result);
		 
		curl_close($curl);
		 
		if ((string)$xmlObject->ErrorCode !== 'Ok') {
			$RETURN_DATA['STATUS'] = 'ERROR';
			$RETURN_DATA['MSG'] = "API Service Error 1 : " . $xmlObject->ErrorDescription;
			return $RETURN_DATA;
		    // echo json_encode($RETURN_DATA);die();
		}
		 
		$itemInfo = $xmlObject->Result->Item;
		// print_r($itemInfo);
		// \Log::info(print_r($itemInfo, true));
		//exit;
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

		$itemAttributes = array();
		$arr_color_img = array();
		$arr_color = array();
		$arr_original_color = array();
		$arr_size = array();
		$loop = 100;
		if (isset($itemInfo->Attributes->ItemAttribute)) {
		    foreach ($itemInfo->Attributes->ItemAttribute as $ItemAttribute) {
		       
				// if(strtolower(trim($ItemAttribute->PropertyName)) == 'colour' || strtolower(trim($ItemAttribute->PropertyName)) == 'color classification' || strtolower(trim($ItemAttribute->PropertyName)) == 'primary color' || strtolower(trim($ItemAttribute->PropertyName)) == 'model' || strtolower(trim($ItemAttribute->PropertyName)) == 'taste' || strtolower(trim($ItemAttribute->PropertyName)) == 'net weight'){
		    	//if(isset($ItemAttribute->PropertyName) && strpos(strtolower($ItemAttribute->PropertyName), 'size') == false){
		    	if(isset($ItemAttribute->IsConfigurator) && 
		    		$ItemAttribute->IsConfigurator == 'true' && 
		    		strpos(strtolower($ItemAttribute->PropertyName), 'size') === false &&
		    		strpos(strtolower($ItemAttribute->PropertyName), 'height') === false
		    		){
		    		$arr_original_color[] = (string)$ItemAttribute->OriginalValue;
				 	$color_val = trim($this->translateWord((string)$ItemAttribute->OriginalValue));//(string)$ItemAttribute->Value;
				 	if(isset($ItemAttribute->ImageUrl) && !$ItemAttribute->IsMain){
				 		$arr_color_img[] = (string)$ItemAttribute->ImageUrl; 
				 	}
					if(isset($ItemAttribute->ValueAlias) && trim($ItemAttribute->ValueAlias) != ''){
						$color_val = (string)$ItemAttribute->ValueAlias;
					}

					// \Log::info(strtolower($ItemAttribute->PropertyName) . ' = ' . $color_val);
					$arr_color[] = $color_val;
				 }
				 
				 else if(strtolower($ItemAttribute->PropertyName) == 'size' || 
				 		strpos(strtolower($ItemAttribute->PropertyName), 'height') !== false){
				 	$arr_size[] = (string)$ItemAttribute->Value;
				 }

				 $loop++;
				 if($loop == 100){
				 	break;
				 }

		    }
		}
		
		$price_list_by_color = array();
		foreach ($itemInfo->ConfiguredItems->OtapiConfiguredItem as $OtapiConfiguredItem) {
			$price_list_by_color[] = (string)$OtapiConfiguredItem->Price->OriginalPrice;
		}

		$ProductLevelList = [];
		$IsHasItems = true;

		foreach($arr_original_color as $k => $v){
			\Log::info('Total OtapiConfiguredItem : ' . count($itemInfo->ConfiguredItems->OtapiConfiguredItem));
			$loop = 0;
			foreach ($itemInfo->ConfiguredItems->OtapiConfiguredItem as $key => $value) {



				if($value->Configurators->ValuedConfigurator[0]['Vid'] == $v){

					// \Log::info('V = ' . $v);
					// \Log::info('Vid = ' . $value->Configurators->ValuedConfigurator[0]['Vid']);

					$quantity = simpleXmlToArray($value)['Quantity'];
					$price = simpleXmlToArray($value->Price)['OriginalPrice'];
					$description_arr = $value->Configurators;
					$description = '';
					$detail_arr = [];
					$prod_id = null;
					$__cnt = 0;
					foreach ($description_arr->ValuedConfigurator as $desc_key => $desc_value) {
						// \Log::info($__cnt . ' :- ' . $desc_value['Vid']);
						if($__cnt == 0){
							$prod_id = trim($this->translateWord($desc_value['Vid']));
						}else{
							$detail_arr[0] = $desc_value['Vid'];	
						}
						
						$__cnt++;
					}

					$description = implode(' ', $detail_arr);
					$translate_description = trim($this->translateWord($description));
					if(!empty($translate_description)){
						$description = $translate_description;
					}

					$detail = ['prod_id' => $prod_id, 'quantity' => $quantity, 'price' => $price, 'description' => $description];
						
					// $data_exists = $this->searchArrayKeyValue($ProductLevelList, 'description', $description);
					// \Log::info($data_exists);
					// if(empty($data_exists)){
					$ProductLevelList[] = $detail;
					// }else{

					// 	for($i = 0; $i < count($ProductLevelList); $i++){
					// 		if($ProductLevelList[$i]['description'] == $description){
					// 			$ProductLevelList[$i] = $detail;
					// 		}
					// 	}

					// }

					// find vid in product color name if exist it is has no items
					if(in_array($prod_id, $arr_color)){
						$IsHasItems = false;
					}
				}

				$loop++;
				if($loop == 20){
					break;
				}
			}

		}

		// usort($ProductLevelList, function($a, $b) {
		//     return $a['prod_id'] <=> $b['prod_id'];
		// });

		// usort($ProductLevelList, function($a, $b) {
		//     return $a['prod_id'] <=> $b['prod_id'];
		// });

		$product_normal_price = floatval($itemInfo->Price->OriginalPrice);
		if(count($price_range_list) > 0){
			$product_normal_price = $price_range_list[0]['price'];
		}
		
		$product_result = array('product_url'=>(string)$itemInfo->ExternalItemUrl
								,'product_original_name'=>(string)$itemInfo->Title
								,'product_image'=>(string)$itemInfo->MainPictureUrl
								,'product_color_img'=>$arr_color_img
								,'product_color'=>$arr_color
								,'product_size'=>$arr_size
								,'product_qty'=>1
								,'product_normal_price'=>$product_normal_price
								,'product_currency_displayname'=>'¥'
								,'product_promotion_price'=>0
								,'merchant_name'=>$itemInfo->VendorName
								,'product_color_img_choose'=>empty($arr_color_img)?[]:$arr_color_img[0]
								,'product_color_choose'=>trim(empty($arr_color)?'':$arr_color[0])
								,'product_size_choose'=>empty($arr_size)?[]:$arr_size[0]
								,'price_list_by_color'=>$price_list_by_color
								,'IsHasItems'=>$IsHasItems
								,'ProductLevelList' => $ProductLevelList
								,'PriceRangeList' => $price_range_list
								,'exchange_rate' =>getLastChinaRate()['exchange_rate']
							);
		\Log::info($product_result);
		return $product_result;
    }

    private function searchArrayKeyValue($array, $key, $value)
	{
	    $results = array();

	    if (is_array($array)) {
	        if (isset($array[$key]) && $array[$key] == $value) {
	            $results[] = $array;
	        }

	        foreach ($array as $subarray) {
	            $results = array_merge($results, $this->searchArrayKeyValue($subarray, $key, $value));
	        }
	    }

	    return $results;
	}

	
}