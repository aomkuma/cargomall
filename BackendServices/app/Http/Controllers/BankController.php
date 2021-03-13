<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;

class BankController extends Controller
{
    //
    public function getListManage(Request $request){

    	$params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);

        $list = BankAccount::all();

        $this->data_result['DATA']['DataList'] = $list;

    	return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function getList(Request $request){

    	$params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);

        $list = BankAccount::where('is_active', true)->get();

        $this->data_result['DATA']['DataList'] = $list;

    	return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function updateData(Request $request){

    	$params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $Data = $params['obj']['Data'];
        $id = null;

        if(empty($Data['id'])){
        	$id = BankAccount::create($Data)->id;
        }else{
        	$cargo_address = BankAccount::find($Data['id']);
        	$cargo_address->update($Data);
        	$id = $cargo_address->id;
        }

        $this->data_result['DATA']['id'] = $id;

    	return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function deleteData(Request $request){

    	$params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $id = $params['obj']['id'];

    	$result = BankAccount::find($id)->delete();
    
        $this->data_result['DATA']['result'] = $result;

    	return $this->returnResponse(200, $this->data_result, response(), false);
    }
}
