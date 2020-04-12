<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $data_result = array('STATUS'=>'OK','DATA'=>null);
        
    public function returnResponse($status, $data, $response, $check_numeric = true){
        ob_clean();
        ob_flush();
        if($check_numeric){
            return $response->json(['data'=>$data]);
        }else{
            return $response->json(['data'=>$data]);
        }
    }
    
    public function returnSystemErrorResponse($logger, $data_result, $e, $response){
        ob_clean();
        ob_flush();
        
        $data_result['STATUS'] = 'ERROR';
        $data_result['DATA'] = $e;
        return $response->json(['data'=>$data]);
    }
}
