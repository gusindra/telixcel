<?php

namespace App\Http\Controllers;

use App\Http\Resources\SmsResource;
use App\Models\BlastMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessSmsApi;
use App\Models\ApiCredential;
use App\Models\Client;

class ApiSmsController extends Controller
{
    /**
     * get all record sms
     *
     * @return void
     */
    public function index()
    {
        $data = BlastMessage::where('user_id', '=', auth()->user()->id)->get();

        return response()->json([
            'code' => 200,
            'message' => "Successful",
            'response' => SmsResource::collection($data),
        ]);
    }

    /**
     * show record sms by phone number
     *
     * @param  mixed $phone
     * @return void
     */
    public function show($phone)
    {
        $customer = Client::where('phone', $phone)->where('user_id', auth()->user()->id)->first();
        if($customer){
            $data = BlastMessage::where('user_id', '=', auth()->user()->id)->where('client_id', $customer->uuid)->get();

            return response()->json([
                'code' => 200,
                'message' => "Successful",
                'response' => SmsResource::collection($data),
            ]);
        }
        return response()->json([
            'code' => 404,
            'message' => "User not found"
        ]);
    }

    /**
     * post new sms
     *
     * @param  mixed $request
     * @return void
     */
    public function post(Request $request)
    {
        //get the request & validate parameters
        $fields = $request->validate([
            'type' => 'required|numeric',
            'to' => 'required|string',
            'from' => 'required|alpha_num',
            'text' => 'required|string',
            'servid' => 'string',
            'title' => 'required|string',
            'detail' => 'string',
        ]);
        // call request to MacroKiosk check by type
        // text / multi
        //check user pass Mk
        // $ada = $request->all();
        // return $ada['type'];
        try{
            $userCredention = ApiCredential::where("user_id", auth()->user()->id)->where("client", "api_sms_mk")->where("is_enabled", 1)->first();
            ProcessSmsApi::dispatch($request->all(), $userCredention);
        }catch(\Exception $e){
            return response()->json([
                'message' => "Failed",
                'code' => 400
            ]);
        }
        // show result on progress
        return response()->json([
            'message' => "Successful",
            'code' => 200
        ]);
    }

    /**
     * send Bulk sms
     *
     * @param  mixed $request
     * @return void
     */
    public function sendBulk(Request $request)
    {
        //get the request & validate parameters
        try{
            $userCredention = ApiCredential::where("user_id", auth()->user()->id)->where("client", "api_sms_mk")->where("is_enabled", 1)->first();
            foreach($request->all() as $arr){
                ProcessSmsApi::dispatch($arr, $userCredention);
            }
        }catch(\Exception $e){
            return response()->json([
                'message' => "Failed",
                'code' => 400
            ]);
        }
        // show result on progress
        return response()->json([
            'message' => "Successful",
            'code' => 200
        ]);
    }
}
