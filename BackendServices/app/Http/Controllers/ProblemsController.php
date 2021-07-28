<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Problem;

use DB;

class ProblemsController extends Controller
{
    //
    public function getList(Request $request){

    	$params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $condition = $params['obj']['condition'];

        $list = Problem::select('problems.*', 'user.user_code', 'user.mobile_no')
                ->join('user', 'user.id', '=', 'problems.user_id')
        		->with('admin')
                ->where(function($query) use ($condition){
                    if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                        $query->where('user_code' , 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                    }
                    if(isset($condition['status']) &&  !empty($condition['status'])){
                        $query->where('status' , $condition['status']);
                    }
                })
        		->orderBy('created_at', 'DESC')
    			->get();

        $this->data_result['DATA']['DataList'] = $list;

    	return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function getListByUser(Request $request){

    	$params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);

        $list = Problem::where('user_id', $user_data['id'])
				->orderBy('created_at', 'DESC')
				->get();

        $this->data_result['DATA']['DataList'] = $list;

    	return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function updateDataUser(Request $request){

    	$params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $Data = $params['obj']['ProblemData'];

        $problem = null;
        if(empty($Data['id'])){

        	$Data['user_id'] = $user_data['id'];
        	$Data['status'] = 'notify';

        	Problem::create($Data);

        }else{

        	$problem = Problem::find($Data['id'])->update($Data);

        }

        $this->data_result['DATA'] = $problem;

    	return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function updateDataAdmin(Request $request){

    	$params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $Data = $params['obj']['ProblemData'];

        $Data['admin_id'] = $user_data['id'];

    	if($Data['status'] == 'close'){
    		$Data['close_datetime'] = date('Y-m-d H:i:s');
    	}
    	$problem = Problem::find($Data['id'])->update($Data);

        $this->data_result['DATA'] = $problem;

    	return $this->returnResponse(200, $this->data_result, response(), false);
    }
}
