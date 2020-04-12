<?php

namespace App\Http\Controllers;

use App\ExchangeRate;
use App\ExchangeRateTransfer;

use Request;

use DB;

use Illuminate\Support\Facades\Log;

class ExchangeRatesController extends Controller
{
    //

    public function getExchangeRateList(){

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

        $totalRows = ExchangeRate::where(function($query) use ($condition){
                    if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                    	
                    	$date = getDateFromString($condition['created_at']);
                        $query->where('created_at','LIKE', DB::raw("'" . $date . "%'"));
                    }
                })
                ->count();

        $list = ExchangeRate::where(function($query) use ($condition){
                    if(isset($condition['created_at']) &&  !empty($condition['created_at'])){

                    	$date = getDateFromString($condition['created_at']);
                        $query->where('created_at','LIKE', DB::raw("'" . $date . "%'"));
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

    public function updateExchangeRate(){

        $params = Request::all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $ExchangeRate = $params['obj']['ExchangeRate'];

        Log::info("UPDATE_EXCHANGE_RATE => ");
        Log::info("User : " . $user_data['firstname'] . ' ' . $user_data['lastname']);

        $exchange_rate = new ExchangeRate;
        if($exchange_rate){

        	$exchange_rate->id = generateID();
            $exchange_rate->exchange_rate = $ExchangeRate['exchange_rate'];
            $exchange_rate->save();

            $this->data_result['DATA'] = true;
        }else{

            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'Order not found';

        }

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function getCurrentExchangeRate(){

    	$exchange_rate = getLastChinaRate();
        $exchange_rate_transfer = getLastChinaRateTransfer();

		$this->data_result['DATA']['exchange_rate'] = $exchange_rate;
        $this->data_result['DATA']['exchange_rate_transfer'] = $exchange_rate_transfer;

    	return $this->returnResponse(200, $this->data_result, response(), false);
    }


    public function getExchangeRateTransferList(){

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

        $totalRows = ExchangeRateTransfer::where(function($query) use ($condition){
                    if(isset($condition['created_at']) &&  !empty($condition['created_at'])){
                        
                        $date = getDateFromString($condition['created_at']);
                        $query->where('created_at','LIKE', DB::raw("'" . $date . "%'"));
                    }
                })
                ->count();

        $list = ExchangeRateTransfer::where(function($query) use ($condition){
                    if(isset($condition['created_at']) &&  !empty($condition['created_at'])){

                        $date = getDateFromString($condition['created_at']);
                        $query->where('created_at','LIKE', DB::raw("'" . $date . "%'"));
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

    public function updateExchangeRateTransfer(){

        $params = Request::all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $ExchangeRate = $params['obj']['ExchangeRate'];

        Log::info("UPDATE_EXCHANGE_RATE_TRANSFER => ");
        Log::info("User : " . $user_data['firstname'] . ' ' . $user_data['lastname']);

        $exchange_rate = new ExchangeRateTransfer;
        if($exchange_rate){

            $exchange_rate->id = generateID();
            $exchange_rate->exchange_rate = $ExchangeRate['exchange_rate'];
            $exchange_rate->save();

            $this->data_result['DATA'] = true;
        }else{

            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'Order not found';

        }

        return $this->returnResponse(200, $this->data_result, response(), false);

    }


}
