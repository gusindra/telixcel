<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestApiController extends Controller
{
    public function get()
    {
        return response()->json([
            'team' => auth()->user(),
            'old' => 'this is get request'
        ]);
    }

    public function show($id)
    {
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
                    'MsgID' => "11888800".$key,
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
                $string = $string.$phone.",11888800".$key.",200,IDR,350|";
            }
            return $string = $string."=99.9500,".$key+1;
        }

        return response()->json([
            'MsgID' => "",
            'Msisdn' => "",
            'Status' => "400"
        ]);
    }
}
