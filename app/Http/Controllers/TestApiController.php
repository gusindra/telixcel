<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestApiController extends Controller
{
    public function get()
    {
        // return 1;
        $data = response()->json([
            'team' => auth()->user(),
            'old' => 'this is get request'
        ]);

        Log::debug($data);

        return $data;
    }

    public function show($id)
    {
        // return $id;
        Log::debug('Job Failed : servid invalid');
        return response()->json([
            'old' => $id
        ]);
    }

    public function post(Request $request)
    {
        return response()->json([
            'new' => $request
        ]);
    }

    public function put($id, Request $request)
    {
        return response()->json([
            'old' => $id,
            'new' => $request
        ]);
    }

    public function smsbulk(Request $request)
    {
        $phones = explode (",", $request->to);
        $headers = $request->header('accept');
        if($headers == 'application/json'){
            $array = [];
            foreach($phones as $key => $phone){
                $array[$key] = [
                    'MsgID' => date("YmdHis").rand(1,10),
                    'Msisdn' => $phone,
                    'Status' => "200",
                    'Currency' => "IDR",
                    'Price' => "200"
                ];
            }
            $array[$key+1] = [
                'Balance' => "99.8500",
                'TotalMSISDN' => $key+1
            ];
            return response()->json([
                $array
            ]);
        }else{
            // return "400";
            $string = "";
            foreach($phones as $key => $phone){
                // $string = $string.$phone.",".date("YmdHis").rand(1,10).",200,IDR,450|";
                if(count($phones)==1 || count($phones)==$key+1){
                    $string = $string.$phone.",".date("YmdHis").rand(1,10).",200,IDR,450";
                }else{
                    $string = $string.$phone.",".date("YmdHis").rand(1,10).",200,IDR,450|";
                }
            }
            // return $string = $string; //."=99.9500,".$key+1;
            return $string = $string;
        }

        return response()->json([
            'MsgID' => "",
            'Msisdn' => "",
            'Status' => "400"
        ]);
    }
}
