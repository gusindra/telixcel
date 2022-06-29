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
use Illuminate\Support\Str;

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
            'servid' => 'required|string',
            'title' => 'required|string',
            'detail' => 'string',
        ]);
        // return response()->json([
        //     'message' => "Successful",
        //     'code' => 200
        // ]);

        try{
            //$userCredention = ApiCredential::where("user_id", auth()->user()->id)->where("client", "api_sms_mk")->where("is_enabled", 1)->first();
            //Log::debug('call sms api');
            // ProcessSmsApi::dispatch($request->all(), auth()->user());
            $phones = explode(",", $request->to);
            $balance = (int)balance(auth()->user());
            if($balance>500 && count($phones)<$balance/500){
                ProcessSmsApi::dispatch($request->all(), auth()->user());
            }else{
                return response()->json([
                    'message' => "Insufficient Balance",
                    'code' => 405
                ]);
            }
            //$this->sendSMS($request->all());
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
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
        try{
            foreach($request->all() as $sms){
                ProcessSmsApi::dispatch($sms, auth()->user());
            }
        }catch(\Exception $e){
            return response()->json([
                'message' => $e->getMessage(),
                'code' => 400
            ]);
        }
        // show result on progress
        return response()->json([
            'message' => "Successful",
            'code' => 200
        ]);
    }

    private function sendSMS($request){

        $user   = 'TCI01';
        $pass   = 'IFc21bL+';
        $serve  = 'mes01';
        $msg    = "";

        // if(array_key_exists('servid', $request)){
        //     $serve  = $request['servid'];
        // }
        if($serve==$request['servid']){
            // $url = 'http://www.etracker.cc/bulksms/mesapi.aspx';
            $url = 'http://telixcel.com/api/send/smsbulk';

            $response = '';
            if($request['type']=="0"){
                //accept('application/json')->
                $response = Http::get($url, [
                    'user'  => $user,
                    'pass'  => $pass,
                    'type'  => $request['type'],
                    'to'    => $request['to'],
                    'from'  => $request['from'],
                    'text'  => $request['text'],
                    'servid' => $serve,
                    'title' => $request['title'],
                    'detail' => 1,
                ]);
            }

            // check response code
            if($response=='400'){
                $msg = "Missing parameter or invalid field type";
            }elseif($response=='401'){
                $msg = "Invalid username, password or ServID";
            }elseif($response=='402'){
                $msg = "Invalid Account Type (when call using postpaid client’s account)";
            }elseif($response=='403'){
                $msg = "Invalid Email Format";
            }elseif($response=='404'){
                $msg = "Invalid MSISDN Format";
            }elseif($response=='405'){
                $msg = "Invalid Balance Tier Format";
            }elseif($response=='500'){
                $msg = "System Error";
            }else{
                //Log::debug('process array result');
                $array_res = [];
                $res = explode("|", $response);
                $res_end = [];
                //Log::debug('array start');
                foreach($res as $k1 => $data){
                    $data_res = explode (",", $data);
                    foreach($data_res as $k2 => $data){
                        if(count($res)==$k1+1){
                            $res_end[$k2] = $data;
                        }else{
                            $array_res[$k1][$k2] = $data;
                        }
                    }
                }
                // Log::debug($res_end);
                foreach ($array_res as $msg_msis){
                    // Log::debug($this->chechClient("200", $msg_msis[0]));
                    $modelData = [
                        'msg_id'    => $msg_msis[1],
                        'user_id'   => auth()->user()->id,
                        'client_id' => $this->chechClient("200", $msg_msis[0]),
                        'type'      => $request['type'],
                        'status'    => "PROCESSED",
                        'code'      => $msg_msis[2],
                        'message_content'  => $request['text'],
                        'currency'  => $msg_msis[3],
                        'price'     => $msg_msis[4],
                        'balance'   => $res_end[0],
                        'msisdn'    => $msg_msis[0],
                    ];
                    // Log::debug($modelData);
                    BlastMessage::create($modelData);
                }
            }
        }else{
            abort(404, "Serve ID is wrong");
        }

        if($msg!=''){
            $this->saveResult($msg, $request);
        }
    }

    private function saveResult($msg, $request){
        $user_id = auth()->user()->id;
        $modelData = [
            'msg_id'    => 0,
            'user_id'   => $user_id,
            'client_id' => $this->chechClient("400", null, $request),
            'type'      => $request['type'],
            'status'    => $msg,
            'code'      => "400",
            'message_content'  => $request['text'],
            'price'     => 0,
            'balance'   => 0,
            'msisdn'    => 0,
        ];
        BlastMessage::create($modelData);
    }

    private function chechClient($status, $msisdn=null, $request=null){
        $user_id = auth()->user()->id;
        if($status=="200"){
            $client = Client::where('phone', $msisdn)->where('user_id', $user_id)->firstOr(function () use ($msisdn, $user_id) {
                return Client::create([
                    'phone' => $msisdn,
                    'user_id' => $user_id,
                    'uuid' => Str::uuid()
                ]);
            });
        }else{
            $phones = explode (",", $request['to']);
            $client = Client::where('phone', $phones[0])->where('user_id', $user_id)->firstOr(function () use ($phones, $user_id) {
                return Client::create([
                    'phone' => $phones[0],
                    'user_id' => $user_id,
                    'uuid' => Str::uuid()
                ]);
            });
        }

        return $client->uuid;
    }
}
