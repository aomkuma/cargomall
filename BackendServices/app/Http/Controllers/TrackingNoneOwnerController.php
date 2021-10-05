<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\TrackingNoneOwner;
use App\Models\CustomerRequestOwner;
use App\Models\OrderTrackingNotOwner;
use App\OrderTracking;
use App\Order;

class TrackingNoneOwnerController extends Controller
{
    //

    public function getList(Request $request){

    	$params = $request->all();
        // $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $condition = $params['obj']['condition'];

        $list = TrackingNoneOwner::with('orderTrackingNotOwner')
                ->where('track_status', 1);

        if(!empty($condition['limit'])){
        	$list = $list->take($condition['limit']);
        }
        
        $list = $list->orderBy('created_at', 'DESC')->get();

        $this->data_result['DATA']['DataList'] = $list;

    	return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function getListActive(Request $request){

        $params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $condition = $params['obj']['condition'];


        $list = TrackingNoneOwner::select('tracking_none_owner.id', 
                                        'order_tracking_not_owner.tracking_no',
                                        'order_tracking_not_owner.order_status',
                                        'tracking_none_owner.track_status',
                                        'tracking_none_owner.image_path'
                                    )
                ->with('customer')
                ->where('track_status', 1)
                ->join('order_tracking_not_owner', 'order_tracking_not_owner.id' , '=' , 'tracking_none_owner.tracking_id');

        if(!empty($condition['tracking_no'])){
            $list = $list->where('order_tracking_not_owner.tracking_no', $condition['tracking_no']);
        }

        $list = $list->get();

        $this->data_result['DATA']['DataList'] = $list;

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function getListManage(Request $request){

        $params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $condition = $params['obj']['condition'];

        $list = TrackingNoneOwner::select('tracking_none_owner.id', 
                                        'order_tracking_not_owner.tracking_no',
                                        'order_tracking_not_owner.order_status',
                                        'tracking_none_owner.track_status',
                                        'tracking_none_owner.image_path'
                                    )
                ->with('customer')
                ->join('order_tracking_not_owner', 'order_tracking_not_owner.id' , '=' , 'tracking_none_owner.tracking_id');

        if(!empty($condition['tracking_no'])){
            $list = $list->where('order_tracking_not_owner.tracking_no', $condition['tracking_no']);
        }

        $list = $list->get();

        $this->data_result['DATA']['DataList'] = $list;

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function getDataManage(Request $request){

        $params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $id = $params['obj']['id'];

        $data = TrackingNoneOwner::/*with('orderTrackingNotOwner')->*/find($id);
        $order_tracking_data = OrderTrackingNotOwner::find($data->tracking_id);
        // $order_data = Order::find($order_tracking_data->order_id);

        // get who request
        // $customer_req_owner = CustomerRequestOwner::with('customer')->where('tracking_none_owner_id', $id)->get();
        $customer_req_owner = CustomerRequestOwner::select('customer_request_owner.*', 'user.firstname', 'user.lastname', 'user.mobile_no', 'user_code')
                            ->join('user', 'user.id', '=', 'customer_request_owner.user_id')
                            ->where('tracking_none_owner_id', $id)
                            ->get();

        $this->data_result['DATA']['Data'] = $data;
        // $this->data_result['DATA']['OrderData'] = $order_data;
        $this->data_result['DATA']['OrderTrackingData'] = $order_tracking_data;
        $this->data_result['DATA']['CustomerRequestOwner'] = $customer_req_owner;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    public function updateData(Request $request){

        $params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $Data = $params['obj']['Data'];
        $OrderTrackingDataNoneOwner = $params['obj']['OrderTrackingDataNoneOwner'];

        $id = null;
        $tracking_id = null;

        if(empty($Data['id'])){

            $OrderTrackingDataNoneOwner['id'] = generateID();
            $tracking_data = OrderTrackingNotOwner::create($OrderTrackingDataNoneOwner);

            $tracking_id = $OrderTrackingDataNoneOwner['id'];
            $Data['tracking_id'] = $tracking_id;
            $Data['created_by'] = $user_data['id'];
            // print_r($Data);exit;
            $data = TrackingNoneOwner::create($Data);
            $id = $data->id;
            

        }else{
            $data = TrackingNoneOwner::find($Data['id']);
            $data->update($Data);
            $id = $data->id;
            $tracking_data = OrderTrackingNotOwner::find($OrderTrackingDataNoneOwner['id']);
            if($tracking_data){
                $tracking_data->update($OrderTrackingDataNoneOwner);
                $tracking_id = $tracking_data->id;
            }
            

        }
        

        $this->data_result['DATA']['id'] = $id;
        $this->data_result['DATA']['tracking_id'] = $tracking_id;

        return $this->returnResponse(200, $this->data_result, response(), false);
        
    }

    public function uploadData(Request $request){

        $params = $request->all();
        $file = $request->file();
        // $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $id = $params['obj']['id'];

        $AttachFile = $file['obj']['AttachFile'];

        if(empty($AttachFile->getClientOriginalExtension())){
            $this->data_result['STATUS'] = 'ERROR';
            $this->data_result['DATA'] = 'Invalid request';
            return $this->returnResponse(200, $this->data_result, response(), false);
        }

        // $Data = $params['obj']['Data'];
        // $Data['user_id'] = ''.$Data['user_id'];
        // upload file
        $image_path = $AttachFile->storeAs(
            'track_none_owner', $id . '_' . date('YmdHis')  . '.' . $AttachFile->getClientOriginalExtension()
        );

        $track_none_owner = TrackingNoneOwner::find($id);
        $track_none_owner->image_path = $image_path;
        $track_none_owner->save();

        $this->data_result['DATA']['image_path'] = $image_path;

        return $this->returnResponse(200, $this->data_result, response(), false);

    }

    
    public function addData(Request $request){

    	$params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $id = $params['obj']['id'];

        $order_tracking = OrderTracking::find($id);
        $order_tracking->is_tracking_none_owner = true;
        $order_tracking->save();
        
        $add_data = [];
        $add_data['tracking_id'] = $id;
        $add_data['track_status'] = 1;
        $add_data['created_by'] = $user_data['id'];

        $result = TrackingNoneOwner::create($add_data);

        $this->data_result['DATA']['result'] = $result;

    	return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function requestToBeOwner(Request $request){

        $params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $id = $params['obj']['id'];

        $add_data = [];
        $add_data['tracking_none_owner_id'] = $id;
        $add_data['status'] = 0;
        $add_data['user_id'] = $user_data['id'];

        $result = CustomerRequestOwner::create($add_data);

        $this->data_result['DATA']['result'] = $result;

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function addToBeOwner(Request $request){

        $params = $request->all();
        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $data_id = $params['obj']['data_id'];
        $user_id = $params['obj']['user_id'];

        $cus_req_owner = CustomerRequestOwner::find($user_id);
        $cus_req_owner->status = 1;
        $cus_req_owner->save();

        // update TrackingNoneOwner status to received
        $track_none_owner = TrackingNoneOwner::find($data_id);
        $track_none_owner->track_status = 2;
        $track_none_owner->save();

        $this->data_result['DATA']['result'] = true;

        return $this->returnResponse(200, $this->data_result, response(), false);
    }
}
