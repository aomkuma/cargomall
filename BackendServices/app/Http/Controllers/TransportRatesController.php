<?php

namespace App\Http\Controllers;

use App\TransportRate;

use Request;

class TransportRatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {

        $list = TransportRate::groupBy('rate_level')->get();
        $this->data_result['DATA'] = $list;

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function get()
    {
        //
        $data = [];
        $params = Request::all();
        $rate_level = $params['obj']['rate_level'];

        if(empty($rate_level)){
            $rate_level = 'ทั่วไป';
        }
        $data['rate_sea_kg'] = TransportRate::where('rate_level', $rate_level)->where('transport_type', 'sea')->where('rate_by_condition', 'kg')->orderBy('created_at', 'ASC')->get();
        $data['rate_sea_cbm'] = TransportRate::where('rate_level', $rate_level)->where('transport_type', 'sea')->where('rate_by_condition', 'cbm')->orderBy('created_at', 'ASC')->get();

        $data['rate_car_kg'] = TransportRate::where('rate_level', $rate_level)->where('transport_type', 'car')->where('rate_by_condition', 'kg')->orderBy('created_at', 'ASC')->get();
        $data['rate_car_cbm'] =  TransportRate::where('rate_level', $rate_level)->where('transport_type', 'car')->where('rate_by_condition', 'cbm')->orderBy('created_at', 'ASC')->get();

        $this->data_result['DATA'] = $data;

        return $this->returnResponse(200, $this->data_result, response(), false);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\TransportRate  $transportRate
     * @return \Illuminate\Http\Response
     */
    public function show(TransportRate $transportRate)
    {
        //
        $data = [];
        $rate_level = 'ทั่วไป';
        $data['rate_sea_kg'] = TransportRate::where('rate_level', $rate_level)->where('transport_type', 'sea')->where('rate_by_condition', 'kg')->orderBy('created_at', 'ASC')->get();
        $data['rate_sea_cbm'] = TransportRate::where('rate_level', $rate_level)->where('transport_type', 'sea')->where('rate_by_condition', 'cbm')->orderBy('created_at', 'ASC')->get();

        $data['rate_car_kg'] = TransportRate::where('rate_level', $rate_level)->where('transport_type', 'car')->where('rate_by_condition', 'kg')->orderBy('created_at', 'ASC')->get();
        $data['rate_car_cbm'] =  TransportRate::where('rate_level', $rate_level)->where('transport_type', 'car')->where('rate_by_condition', 'cbm')->orderBy('created_at', 'ASC')->get();

        $this->data_result['DATA'] = $data;

        return $this->returnResponse(200, $this->data_result, response(), false);
    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\TransportRate  $transportRate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TransportRate $transportRate)
    {
        //
        $params = Request::all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $Data = $params['obj']['Data'];
        
        $type = '';
        if(isset($params['obj']['Data']['id'])){
            $type = 'edit';
            $id = trim($params['obj']['Data']['id']);
            unset($Data['id']);
            TransportRate::find($id)->update($Data);

        }else{
            $type = 'add';
            $Data['id'] = generateID();
            TransportRate::create($Data);
        }

        $this->data_result['DATA'] = $type;

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

    public function updateRateLevel(Request $request)
    {
        //
        $params = Request::all();

        $user_data = json_decode( base64_decode($params['user_session']['user_data']) , true);
        $user_data['id'] = ''.$user_data['id'];
        $Data = $params['obj']['Data'];
        
        $rate_level = 'ทั่วไป';

        $data['rate_sea_kg'] = TransportRate::where('rate_level', $rate_level)->where('transport_type', 'sea')->where('rate_by_condition', 'kg')->orderBy('created_at', 'ASC')->get()->toArray();
        foreach ($data['rate_sea_kg'] as $key => $value) {
            $value['id'] = generateID();
            $value['rate_level'] = $Data['rate_level'];
            TransportRate::create($value);
        }

        $data['rate_sea_cbm'] = TransportRate::where('rate_level', $rate_level)->where('transport_type', 'sea')->where('rate_by_condition', 'cbm')->orderBy('created_at', 'ASC')->get()->toArray();
        foreach ($data['rate_sea_cbm'] as $key => $value) {
            $value['id'] = generateID();
            $value['rate_level'] = $Data['rate_level'];
            TransportRate::create($value);
        }

        $data['rate_car_kg'] = TransportRate::where('rate_level', $rate_level)->where('transport_type', 'car')->where('rate_by_condition', 'kg')->orderBy('created_at', 'ASC')->get()->toArray();
        foreach ($data['rate_car_kg'] as $key => $value) {
            $value['id'] = generateID();
            $value['rate_level'] = $Data['rate_level'];
            TransportRate::create($value);
        }

        $data['rate_car_cbm'] =  TransportRate::where('rate_level', $rate_level)->where('transport_type', 'car')->where('rate_by_condition', 'cbm')->orderBy('created_at', 'ASC')->get()->toArray();
        foreach ($data['rate_car_cbm'] as $key => $value) {
            $value['id'] = generateID();
            $value['rate_level'] = $Data['rate_level'];
            TransportRate::create($value);
        }

        $this->data_result['DATA'] = true;

        return $this->returnResponse(200, $this->data_result, response(), false);
    }

}
