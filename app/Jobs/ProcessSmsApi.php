<?php

namespace App\Jobs;

use App\Models\BlastMessage;
use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessSmsApi implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request;
    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $user)
    {
        $this->request = $request;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Log::debug($this->service);
        //filter OTP & Non OTP
        if($this->request['otp']==false){
            $user   = env('MK_NON_OTP_USER');
            $pass   = env('MK_NON_OTP_PSW');
            $serve  = env('MK_NON_OTP_SERVICE');
        }else{
            $user   = env('MK_OTP_USER');
            $pass   = env('MK_OTP_PSW');
            $serve  = env('MK_OTP_SERVICE');
        }
        $msg    = '';
        // if(array_key_exists('servid', $this->request)){
        //     $serve  = $this->request['servid'];
        // }

        if($serve==strtolower($this->request['servid'])){
            // $url = 'http://www.etracker.cc/bulksms/mesapi.aspx';
            // $url = 'http://telixcel.com/api/send/smsbulk';
            $url = 'http://telixnet.test/api/send/smsbulk';
            $response = '';
            if($this->request['type']=="0"){
                // $response = Http::asForm()->accept('application/xml')->post($url, [
                //     'user' => $user,
                //     'pass' => $pass,
                //     'type' => $request->type,
                //     'to' => $request->to,
                //     'from' => $request->from,
                //     'text' => $request->text,
                //     'servid' => $request->servid,
                //     'title' => $request->title,
                //     'detail' => 1,
                // ]);
                // accept('application/json')->
                $response = Http::get($url, [
                    'user' => $user,
                    'pass' => $pass,
                    'type' => $this->request['type'],
                    'to' => $this->request['to'],
                    'from' => $this->request['from'],
                    'text' => $this->request['text'],
                    'servid' => $serve,
                    'title' => $this->request['title'],
                    'detail' => 1,
                ]);
                // $response = Http::get($url, [
                //     'user' => $user,
                //     'pass' => $pass,
                //     'type' => $this->request['type'],
                //     'to' => $this->request['to'],
                //     'from' => $this->request['from'],
                //     'text' => $this->request['text'],
                //     'servid' => $serve,
                //     'title' => $this->request['title'],
                //     'detail' => 1,
                // ]);
            }
            // return $response;
            // Log::debug($response);
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
                // if (isJSON($response)) {
                //     // JSON is valid
                //     $array_res = json_decode(json_encode(simplexml_load_string($response->getBody()->getContents())), true);
                //     //Log::debug($array_res);
                // }else{
                // }

                $array_res = [];
                $res = explode ("|", $response);
                $balance = 0;
                if(count($res)>0 && strpos($response, '=') !== false){
                    foreach($res as $k1 => $data){
                        $data_res = explode (",", $data);
                        foreach($data_res as $k2 => $data){
                            if(count($res)==$k1+1){
                                $balance = $data;
                            }else{
                                $array_res[$k1][$k2] = $data;
                            }
                        }
                    }
                }else{
                    foreach($res as $k1 => $data){
                        $data_res = explode(",", $data);
                        foreach($data_res as $k2 => $singleData){
                            $array_res[$k1][$k2] = $singleData;
                        }
                    }
                }

                foreach ($array_res as $msg_msis){
                    //check client
                    $modelData = [
                        'msg_id'    => preg_replace('/\s+/', '', $msg_msis[1]),
                        'user_id'   => $this->user->id,
                        'client_id' => $this->chechClient("200", $msg_msis[0]),
                        'sender_id' => $this->request['from'],
                        'type'      => $this->request['type'],
                        'otp'       => $this->request['otp'],
                        'status'    => "PROCESSED",
                        'code'      => $msg_msis[2],
                        'message_content'  => $this->request['text'],
                        'currency'  => $msg_msis[3],
                        'price'     => $msg_msis[4],
                        'balance'   => $balance,
                        'msisdn'    => preg_replace('/\s+/', '', $msg_msis[0]),
                    ];
                    // Log::debug($modelData);
                    BlastMessage::create($modelData);
                }
            }

            if($msg!=''){
                $this->saveResult($msg);
            }
        }else{
            $this->saveResult('invalid servid');
        }

    }

    private function saveResult($msg){
        $user_id = $this->user->id;
        $modelData = [
            'msg_id'            => 0,
            'user_id'           => $user_id,
            'client_id'         => $this->chechClient("400"),
            'type'              => $this->request['type'],
            'status'            => $msg,
            'code'              => "400",
            'message_content'   => $this->request['text'],
            'price'             => 0,
            'balance'           => 0,
            'otp'               => $this->request['otp'],
            'msisdn'            => 0,
        ];
        BlastMessage::create($modelData);
    }

    private function chechClient($status, $msisdn=null){
        $user_id = $this->user->id;
        if($status=="200"){
            $client = Client::where('phone', $msisdn)->where('user_id', $user_id)->firstOr(function () use ($msisdn, $user_id) {
                return Client::create([
                    'phone' => $msisdn,
                    'user_id' => $user_id,
                    'uuid' => Str::uuid()
                ]);
            });
        }else{
            $phones = explode (",", $this->request['to']);
            $client = Client::where('phone', $phones[0])->where('user_id', $user_id)->firstOr(function () use ($phones, $user_id) {
                return Client::create([
                    'phone' => $phones[0],
                    'user_id' => $user_id,
                    'uuid' => Str::uuid()
                ]);
            });
        }
        $team = $this->user->currentTeam;
        $client->teams()->attach($team);

        return $client->uuid;
    }
}
