<?php

namespace App\Http\Controllers;

use Hash;
use DB;

use Carbon\Carbon;

use App\User;
use App\UserSession;
use App\UserAddress;
use App\MoneyBag;
use App\ForgotPassUrl;
use App\TransportRate;


use App\Mail\ForgotPassMail;

use Mail;


use Request;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function forgotPassRequest(){
        $params = Request::all();
        $data = [];
        $data['email'] = $params['obj']['email'];
        // find user
        $user = User::where('email', $data['email'])->first();
        if($user){

            $data_key = generateID();
            $data['user_id'] = $user->id;
            $data['url'] = env('APP_URL') . '/forgot-pass/' . $data_key;
            $data['data_key'] = $data_key;
            $data['active_status'] = 'Y';

            $req = ForgotPassUrl::create($data)->id; 

            $data['firstname'] = $user['firstname'];
            $data['lastname'] = $user['lastname'];

            // Mail::to($data['email'])->send(new ForgotPassMail($data));

        }else{
            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'ไม่พบอีเมลนี้ในระบบ กรุณาตรวจสอบความถูกต้อง';
            return $this->returnResponse(200, $this->data_result, response(), false);
            exit();
        }

        
        $this->data_result['DATA']['MSG'] = 'ระบบได้จัดส่ง URL สำหรับเข้ากรอกรหัสผ่านใหม่ไปยัง email ของท่านเรียบร้อยแล้ว';
        $this->data_result['DATA']['URL'] = $data['url'];
        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function forgotPassCheck(){

        $params = Request::all();
        
        $data_key = $params['obj']['data_key'];
        // find user
        $data = ForgotPassUrl::where('data_key', $data_key)->where('active_status', 'Y')->first();
        if(empty($data)){
            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'Link ไม่ถูกต้อง';
            return $this->returnResponse(200, $this->data_result, response(), false);
            exit();
        }

        $this->data_result['DATA']['email'] = $data['email'];
        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function forgotPassUpdate(){
        
        $params = Request::all();
        $email = $params['obj']['email'];
        $new_password = $params['obj']['password'];
        $data_key = $params['obj']['data_key'];

        // find user
        $check = ForgotPassUrl::where('data_key', $data_key)->update(['active_status' => 'N']);
        
        // update user password
        $password = Hash::make($new_password);

        User::where('email', $email)->update(['password' => $password]);
        
        $this->data_result['DATA'] = true;
        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function testAuth(){
        $this->data_result['DATA'] = 'Auth Pass !';
        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function hashPassword(){
        $d =Carbon::now()->add(-1, 'days')->format('Y-m-d');
        echo $d;exit;
        $password = Request::get("password");
        echo Hash::make('Braze#S3');
    }

    public function getUserList(){

        $users = User::orderBy('user_code',  'ASC')->get();
        $this->data_result['DATA']['DataList'] = $users;
        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function getCustomerList(){

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

        $totalRows = User::with('moneyBags')
                    ->where(function($query) use ($condition){
                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('user_code', 'LIKE', DB::raw("'" . $condition['keyword'] . "%'"));
                            $query->orWhere( DB::raw("CONCAT(firstname , ' ', lastname)"), 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                            $query->orWhere('mobile_no', 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        }
                    })
                    ->count();

        $list = User::with('moneyBags')
                    ->with('addresses')
                    ->where(function($query) use ($condition){
                        if(isset($condition['keyword']) &&  !empty($condition['keyword'])){
                            $query->where('user_code', 'LIKE', DB::raw("'" . $condition['keyword'] . "%'"));
                            $query->orWhere( DB::raw("CONCAT(firstname , ' ', lastname)"), 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                            $query->orWhere('mobile_no', 'LIKE', DB::raw("'%" . $condition['keyword'] . "%'"));
                        }
                    })
                    ->orderBy('created_at', 'DESC')
                    ->skip($skip)
                    ->take($limit)
                    ->get();

        $total_balance = MoneyBag::sum('balance');

        $level_list = TransportRate::groupBy('rate_level')->get();

        $this->data_result['DATA']['DataList'] = $list;
        $this->data_result['DATA']['LevelList'] = $level_list;
        $this->data_result['DATA']['Total'] = $totalRows;
        $this->data_result['DATA']['TotalBalance'] = $total_balance;

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function login()
    {
        //
        // print_r(Request::all());
        $params = Request::all();
        $Login = $params['obj']['LoginObj'];
        // echo ($Login['password']);exit;
        $login_result = User::with(['addresses' => function($q){
                            $q->orderBy('address_no', 'ASC');
                        }])
                        ->with('moneyBags')
                        ->where('email', $Login['email'])
                        // ->where('password', ($Login['password']))
                        ->first();
        // if(Hash::check($Login['password'], $login_result['password'])){
        //     echo 'match';
        // }exit;
        if($login_result && Hash::check(trim($Login['password']), $login_result['password'])){

            // $token = JWTAuth::fromUser($login_result);//exit;
            // create token
            // $UserSession['id'] = generateID();
            // $UserSession['user_id'] = ''.$login_result['id'];
            // $UserSession['created_at'] = Carbon::now();
            // UserSession::create($UserSession);

            $credentials = ["email" => $Login['email'], "password" => $Login['password']];
            // print_r($credentials);exit;
            try {
                if (! $token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'invalid_credentials'], 400);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }

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

    public function register()
    {
        //
        $params = Request::all();
        $Register = $params['obj']['RegisterObj'];
        unset($Register['confirm_password']);

        // check duplicate email and idcard
        $cnt_duplicate = User::where('email', $Register['email'])
                        //->orWhere('idcard', $Register['idcard'])
                        ->count();

        if($cnt_duplicate == 0){
            $Register['id'] = generateID();
            $Register['user_code'] = $this->generateUserCode();
            $Register['created_at'] = Carbon::now();
            $Register['updated_at'] = Carbon::now();
            $Register['password'] = Hash::make($Register['password']);
            $result = User::create($Register);

            // Create money bag
            $money_bag['id'] = generateID();
            $money_bag['user_id'] = $Register['id'];
            $money_bag['balance'] = 0;
            
            MoneyBag::create($money_bag);

            $this->data_result['DATA'] = $result;
        }else{

            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'Duplicate an e-Mail or ID Card';

        }

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function updateUserLevel()
    {
        //
        $params = Request::all();
        $Data = $params['obj']['Data'];

        $user = User::find($Data['id']);
        if($user){
            $user->user_level = $Data['user_level'];
            $user->save();
        }

        return $this->returnResponse(200, $this->data_result, response(), false);
        
    }

    public function updateData()
    {
        //
        $params = Request::all();
        $token = $params['user_session']['token'];
        $UserProfile = $params['obj']['UserProfileObj'];

        $UserProfile['id'] = ''.$UserProfile['id'];

        unset($UserProfile['confirm_password']);

        // check duplicate email and idcard
        $cnt_duplicate = User::where(function ($q) use ($UserProfile){
                            $q->where('email', $UserProfile['email']);
                            // $q->orWhere('idcard', $UserProfile['idcard']);
                        })
                        ->where('id', '<>', $UserProfile['id'])
                        ->count();

        if($cnt_duplicate == 0){

            if(!empty($UserProfile['new_password'])){
                $UserProfile['password'] = Hash::make($UserProfile['new_password']);
                unset($UserProfile['new_password']);
            }

            $UserProfile['updated_at'] = Carbon::now();
            $UserProfile['mobile_no'] = trim(str_replace('-', '', $UserProfile['mobile_no']));
            $result = User::find($UserProfile['id'])->update($UserProfile);

            // Update user addresses
            $UserAddress = $UserProfile['addresses'];
            foreach ($UserAddress as $k => $v) {

                
                if(!isset($v['id'])/* || empty($v['id'])*/){
                    $v['id'] = generateID();
                    $v['user_id'] = $UserProfile['id'];
                    UserAddress::create($v);
                }else{
                    $v['id'] = trim($v['id']);
                    UserAddress::where('id', $v['id'])->update($v);
                }
            }

            $UserData = User::with(['addresses' => function($q){
                            $q->orderBy('address_no', 'ASC');
                        }])
                        ->with('moneyBags')
                        ->where('id', $UserProfile['id'])
                        ->first();

            $this->data_result['DATA']['token'] = $token;
            $this->data_result['DATA']['UserData'] = base64_encode($UserData);

        }else{

            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'Duplicate an e-Mail or ID Card';

        }

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function removeAddress(){
        $params = Request::all();
        $token = $params['user_session']['token'];
        $address_id = '';
        if(isset($params['obj']['address_id'])){
            $address_id = ''.$params['obj']['address_id'];
        }
        $user_id = ''.$params['obj']['user_id'];


        UserAddress::find($address_id)->delete();
        $UserData = User::with('addresses')->where('id', $user_id)->first();

        $this->data_result['DATA']['token'] = $token;
        $this->data_result['DATA']['UserData'] = base64_encode($UserData);

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function getUserAddress(){
        
        $params = Request::all();
        $token = $params['user_session']['token'];
        $user_id = $params['obj']['user_id'];
        
        $data = UserAddress::where('user_id', $user_id)->get();
        
        $this->data_result['DATA']['addresses'] = $data;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    private function generateUserCode(){

        $char_arr = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

        $last_user = User::orderBy('created_at', 'DESC')->first();

        if($last_user){

            $last_user_code = $last_user->user_code;
            $user_code_arr = explode('-', $last_user_code);

            $user_code_char = substr($user_code_arr[1], 0, 1);
            $user_code_integer = substr($user_code_arr[1], 1);    

            $index = array_search($user_code_char, $char_arr);

            if(intval($user_code_integer) == 9999){
                $user_code_integer = 0;
                $index++;
            }

            $user_code_integer += 1;

            $new_user_code = 'CGM-' . $char_arr[$index] . str_pad($user_code_integer, 4,'0', STR_PAD_LEFT);

        }else{

            $new_user_code = 'CGM-A0001';
            
        }

        return $new_user_code;
        

    }

}
