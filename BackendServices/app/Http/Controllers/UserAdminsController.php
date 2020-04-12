<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Hash;
use App\UserAdmin;
use App\UserSession;
use App\Order;
use App\Importer;
use App\MoneyTopup;
use App\MoneyUse;

use DB;

use Illuminate\Support\Facades\Log;

use Request;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserAdminsController extends Controller
{

    //
    public function getMonitorData(){
        $order_list = Order::with('customer')
                ->whereIn('order_status', [2,7])
                ->orderBy('created_at', 'DESC')
                ->get();

        $this->data_result['DATA']['order_list'] = $order_list;

        $importer_list = Importer::with('customer')
                ->whereIn('importer_status', [1,5])
                ->orderBy('created_at', 'DESC')
                ->get();

        $this->data_result['DATA']['importer_list'] = $importer_list;

        $topup_list = MoneyTopup::with('customer')
                ->where('topup_status', 1)
                ->orderBy('created_at', 'ASC')
                ->get();

        $this->data_result['DATA']['topup_list'] = $topup_list;

        $transfer_list = MoneyUse::with('customer')
                ->where('pay_type', 3)
                ->where('pay_status', 1)
                ->orderBy('created_at', 'ASC')
                ->get();

        $this->data_result['DATA']['transfer_list'] = $transfer_list;

        $deposit_list = MoneyUse::with('customer')
                ->where('pay_type', 4)
                ->where('pay_status', 1)
                ->orderBy('created_at', 'ASC')
                ->get();

        $this->data_result['DATA']['deposit_list'] = $deposit_list;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function login()
    {
        //
        // print_r(Request::all());
        $params = Request::all();
        $Login = $params['obj']['LoginObj'];

        $login_result = UserAdmin::where('email', $Login['email'])
                        // ->where('password', $Login['password'])
                        ->first();
        if($login_result && Hash::check(trim($Login['password']), $login_result['password'])){
            $token = JWTAuth::fromUser($login_result);
            // create token
            // $UserSession['id'] = generateID();
            // $UserSession['user_id'] = ''.$login_result['id'];
            // $UserSession['created_at'] = Carbon::now();
            // UserSession::create($UserSession);

            // $credentials = ["email" => $Login['email'], "password" => $Login['password']];
            // print_r($credentials);exit;
            // try {
            //     if (! $token = JWTAuth::attempt($credentials)) {
            //         return response()->json(['error' => 'invalid_credentials'], 400);
            //     }
            // } catch (JWTException $e) {
            //     return response()->json(['error' => 'could_not_create_token'], 500);
            // }

            $login_result['is_admin'] = true;
            unset($login_result['password']);

            $this->data_result['DATA']['token'] = $token;
            $this->data_result['DATA']['UserData'] = base64_encode($login_result);
            // $this->data_result['DATA']['UserDataNotEncode'] = ($login_result);

        }else{

            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'User not found';

        }
        // print_r($Login);
        // $this->data_result['DATA'] = $Login;
        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function logout(){
        $params = Request::all();
        $token = $params['user_session']['token'];
        // $obj = UserSession::find($token);
        // $obj->delete();
        // JWTAuth::parseToken()->invalidate();

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

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

        $totalRows = UserAdmin::where(function($query) use ($condition){
                    if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                        
                        $query->where(DB::raw("CONCAT(firstname , ' ', lastname)"), 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                    }
                })
                ->count();

        $list = UserAdmin::where(function($query) use ($condition){
                    if(isset($condition['keyword']) &&  !empty($condition['keyword'])){

                        $query->where(DB::raw("CONCAT(firstname , ' ', lastname)"), 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
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

    public function get(){

        $params = Request::all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $id = $params['obj']['id'];

        $admin = UserAdmin::find($id);

        $this->data_result['DATA'] = $admin;

        return $this->returnResponse(200, $this->data_result, response(), true);

    }    

    public function update(){

        $params = Request::all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $Data = $params['obj']['Data'];

        Log::info("UPDATE_USER_ADMIN => ");
        Log::info("User : " . $user_data['firstname'] . ' ' . $user_data['lastname']);

        $admin = UserAdmin::find($Data['id']);
        if(!$admin){

            $admin = new UserAdmin();
            $Data['id'] = generateID();
            $Data['password'] = Hash::make($Data['password']);
            $result = $admin->create($Data);
            
        }else{

            if(empty($Data['new_password'])){
                unset($Data['password']);
            }else{
                $Data['password'] = Hash::make($Data['new_password']);
            }

            $result = $admin->update($Data);
        }

        $this->data_result['DATA'] = $result;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

}
