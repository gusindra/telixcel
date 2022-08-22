<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\ProcessSmsApi;
use App\Jobs\ProcessSmsStatus;
use App\Models\ApiCredential;

class ApiBulkSmsController extends Controller
{
    public function post(Request $request)
    {
        //get the request & validate parameters
        $fields = $request->validate([
            'type' => 'required|numeric',
            'to' => 'required|string',
            'from' => 'required|alpha_num',
            'text' => 'required|string',
            'servid' => 'required|string',
            'title' => 'required|string',
            'detail' => 'string',
        ]);

        try{
            $userCredention = ApiCredential::where("user_id", auth()->user()->id)->where("client", "api_sms_mk")->where("is_enabled", 1)->first();
            ProcessSmsApi::dispatch($request->all(), $userCredention);
        }catch(\Exception $e){
            return response()->json([
                'Msg' => "Failed",
                'Status' => 400
            ]);
        }
        // show result on progress
        return response()->json([
            'Msg' => "Successful",
            'Status' => 200
        ]);
    }

    /**
     * status
     *
     * @param  mixed $request->msgid
     * @param  mixed $request->msisdn
     * @param  mixed $request->status
     * @return void
     */
    public function status(Request $request)
    {
        ProcessSmsStatus::dispatch($request->all());

        return response()->json([
            'Msg' => "Process to update",
            'Status' => 200
        ]);
    }
}
