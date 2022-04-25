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
    public $service;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request, $service)
    {
        $this->request = $request;
        $this->service = $service;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug($this->service);

        // call request to MacroKiosk check by type
        // text / multi
        //check user pass Mk
        $user   = $this->service->api_key;
        $pass   = $this->service->server_key;
        $serve  = 'MGI003';

        if(array_key_exists('servid', $this->request)){
            $serve  = $this->request['servid'];
        }

        // $url = 'http://www.etracker.cc/bulksms/mesapi.aspx';
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
            // $response = Http::accept('application/json')->get($url, [
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
        }
        // return $response;
        // Log::debug($response);
        // check response code
        if($response=='400'){
            $msg = "Missing parameter or invalid field type";
            $this->saveResult($msg);
        }elseif($response=='401'){
            $msg = "Invalid username, password or ServID";
            $this->saveResult($msg);
        }elseif($response=='402'){
            $msg = "Invalid Account Type (when call using postpaid client’s account)";
            $this->saveResult($msg);
        }elseif($response=='403'){
            $msg = "Invalid Email Format";
            $this->saveResult($msg);
        }elseif($response=='404'){
            $msg = "Invalid MSISDN Format";
            $this->saveResult($msg);
        }elseif($response=='405'){
            $msg = "Invalid Balance Tier Format";
            $this->saveResult($msg);
        }elseif($response=='500'){
            $msg = "System Error";
            $this->saveResult($msg);
        }else{
            $array_res = [];
            if (isJSON($response)) {
                // JSON is valid
                $array_res  = json_decode(json_encode(simplexml_load_string($response->getBody()->getContents())), true);
            }else{
                $res = explode ("|", $response);
                $res_end = [];
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

                foreach ($array_res as $msg_msis){
                    //check client
                    $blast = new BlastMessage();
                    $blast->msg_id = $msg_msis[1];
                    $blast->user_id = $this->service->user_id;
                    $blast->client_id = $this->chechClient("200", $msg_msis[0]);
                    $blast->type = $this->request['type'];
                    $blast->status = "PROCESSED";
                    $blast->message_content = $this->request['text'];
                    $blast->balance = $res_end[0];
                    $blast->msisdn = $msg_msis[0];
                    $blast->save();
                }
            }
            $msg = "Successful";
        }
    }

    private function saveResult($msg){
        $blast = new BlastMessage();
        $blast->msg_id = 0;
        $blast->user_id = $this->service->user_id;
        $blast->client_id = $this->chechClient("400");
        $blast->type = $this->request['type'];
        $blast->status = $msg;
        $blast->message_content = $this->request['text'];
        $blast->balance = 0;
        $blast->msisdn = 0;
        $blast->save();
    }

    private function chechClient($status, $msisdn=null){
        if($status=="200"){
            $client = Client::where('phone', $msisdn)->where('user_id', $this->service->user_id)->firstOr(function () use ($msisdn) {
                return Client::create([
                    'phone' => $msisdn,
                    'user_id' => $this->service->user_id,
                    'uuid' => Str::uuid()
                ]);
            });
        }else{
            $phones = explode (",", $this->request['to']);
            $client = Client::where('phone', $phones[0])->where('user_id', $this->service->user_id)->firstOr(function () use ($phones) {
                return Client::create([
                    'phone' => $phones[0],
                    'user_id' => $this->service->user_id,
                    'uuid' => Str::uuid()
                ]);
            });
        }

        return $client->uuid;
    }
}
