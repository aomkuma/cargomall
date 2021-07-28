<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\CargoAddress;

class CargoAddressController extends Controller
{
    //
    public function getListManage(Request $request){

    	$params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);

        $list = CargoAddress::all();

        $this->data_result['DATA']['DataList'] = $list;

    	return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function getList(Request $request){

    	$params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        
        $list = CargoAddress::where('is_active', true)->get();

        $CargoList = [];
        foreach ($list as $key => $value) {
            # code...
            $value->address = str_replace('[[[user_code]]]', '<span style="color:red;">' . $user_data['user_code'] . '</span>', $value->address);
            $value->address = str_replace('[[[mobile_no]]]', '<span style="color:red;">' . $user_data['mobile_no'] . '</span>', $value->address);
            $value->address = str_replace('[[[email]]]', '<span style="color:red;">' . $user_data['email'] . '</span>', $value->address);
            $value->address = str_replace('[[[firstname]]]', '<span style="color:red;">' . $user_data['firstname'] . '</span>', $value->address);
            $value->address = str_replace('[[[lastname]]]', '<span style="color:red;">' . $user_data['lastname'] . '</span>', $value->address);

            $CargoList[] = $value;
        }

        $this->data_result['DATA']['DataList'] = $CargoList;

    	return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function updateData(Request $request){

    	$params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $Data = $params['obj']['Data'];
        $id = null;

        if(empty($Data['id'])){
        	$id = CargoAddress::create($Data)->id;
        }else{
        	$cargo_address = CargoAddress::find($Data['id']);
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

    	$result = CargoAddress::find($id)->delete();
    
        $this->data_result['DATA']['result'] = $result;

    	return $this->returnResponse(200, $this->data_result, response(), false);
    }
}
