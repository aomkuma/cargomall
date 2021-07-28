<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;

use App\LandingPage;

use Request;

use DB;

class LandingPageController extends Controller
{
    //


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

        $totalRows = LandingPage::where(function($query) use ($condition){
                    
                })
                ->count();

        $list = LandingPage::where(function($query) use ($condition){
                    
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

        $id = $params['obj']['id'];

        $data = LandingPage::find($id);
        $data['text_desc'] = base64_decode($data['text_desc']);

        $this->data_result['DATA'] = $data;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function show(){

    	$cur_date = date('Y-m-d');
        $data = LandingPage::where('active_status', 'Y')
        		->where('start_date', '<=', $cur_date)
        		->where('end_date', '>=', $cur_date)
        		->first();
        if($data){
            $data['text_desc'] = base64_decode($data['text_desc']);
        }
        
        $this->data_result['DATA'] = $data;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function update(){

    	$params = Request::all();
    	$file = Request::file();
    	$Data = $params['obj']['Data'];

    	$_Data = [];
    	foreach ($Data as $key => $value) {
            if($value == 'null'){
                $_Data[$key] = NULL;
            }else{
            	$_Data[$key] = $value;
            }
        }

        $Data = $_Data;

    	if($file){
	        $AttachFile = $file['obj']['AttachFile'];
	        if(empty($AttachFile->getClientOriginalExtension())){
	            $this->data_result['STATUS'] = 'ERROR';
	            $this->data_result['DATA'] = 'Invalid request';
	            return $this->returnResponse(200, $this->data_result, response(), false);
	        }

	        $landing_path = null;
	        if($AttachFile){
		        $landing_path = $AttachFile->storeAs(
		            'landing_page', 'landing__'. date('YmdHis')  . '.' . $AttachFile->getClientOriginalExtension()
		        );
		    }
	        
	        $Data['image_path'] = 'BackendServices/storage/app/' . $landing_path;
	        
	    }

        $Data['text_desc'] = base64_encode($Data['text_desc']);
    	
        if(empty($Data['id'])){

	        $Data['id'] = generateID();
	        $result = LandingPage::create($Data);

	    }else{
	    	LandingPage::find($Data['id'])->update($Data);
	    }

        return $this->returnResponse(200, $this->data_result, response(), false);

    }
}
