<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Request as Chat;
use App\Models\Client;
use App\Models\Template;
use App\Models\ApiCredential;
use Illuminate\Support\Str;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Http;

class ApiWaController extends Controller
{
    public function show($messege)
    {
        return response()->json([
            'from'  => 'glover.kolby@example.org',
            'state' => $messege
        ]);
    }

    /**
     * API to retrive new message
     * from 3rd party unofficial Whatapps
     * Postman
     *
     * @param  mixed $request Body raw
     * @return void
     */
    public function retriveNewMessage(Request $request)
    {
        // $bodyContent = $request->getContent();
        $data= json_decode($request->getContent(), true);

        // First check from
        // if not exsist add client
        $client = $this->checkClient($data['message']);
        // then add data to Request
        $client_uuid = $client->uuid;
        $client_id = $client->id;
        $user_id = $client->user_id;

        //if chat type text
        $request = Chat::create([
            'source_id' => $data['message']['id'],
            'reply'     => $data['message']['text']['body'],
            'from'      => $client_id,
            'user_id'   => $user_id,
            'type'      => $data['message']['type'],
            'client_id' => $client_uuid,
            'sent_at'   => date('Y-m-d H:i:s', $data['message']['timestamp']),
        ]);

        return $request;
        return $data['message']['from'];
    }

    /**
     * API to retrive new message
     * from 3rd party unofficial Whatapps
     * Postman
     *
     * @param  mixed $request Body raw
     * @return void
     */
    public function index(Request $request, $slug)
    {
        $id = Hashids::decode($slug)[0];
        $userCredention = ApiCredential::find($id);
        // $bodyContent = $request->getContent();
        $data= json_decode($request->getContent(), true);

        // First check from
        // if not exsist add client
        $client = $this->checkClient($data['message']);
        // then add data to Request
        $client_uuid = $client->uuid;
        $client_id = $client->id;
        $user_id = $client->user_id;

        //if chat type text
        $request = Chat::create([
            'source_id' => $data['message']['id'],
            'reply'     => $data['message']['text']['body'],
            'from'      => $client_id,
            'user_id'   => $user_id,
            'type'      => $data['message']['type'],
            'client_id' => $client_uuid,
            'sent_at'   => date('Y-m-d H:i:s', $data['message']['timestamp']),

        ]);

        return $request;
        return $data['message']['from'];
    }

    /**
     * The checker for client
     *
     * @return object
     */
    public function checkClient($message)
    {
        $last_request = Chat::with('client')->where('source_id', $message['id'])->first();
        // Client::where('source_id', $message['id'])->where('from', $message['from'])->first();

        if($last_request){
            $client = $last_request->client;
        }else{
            // Need to find user base on credential
            $user_id = 1;

            $client = Client::create([
                'uuid'      => Str::uuid(),
                'sender'    => $message['name'],
                'name'      => $message['name'],
                'phone'     => $message['from'],
                'user_id'   => $user_id
            ]);
        }

        return $client;
    }

    /**
     * check Endpoint api
     *
     * @return void
     */
    public function checkEndpoint()
    {
        $request = Chat::find(104);
        $template = Template::find(9);
        //$response = Http::asForm()->post('https://api.sitcline.com/svl/voyageInfo/rate?vesselName=SITC+SURABAYA&year=2021&mouth=&month=4');
        $url = $template->endpoint->endpoint;
        if($template->endpoint->request == 'post'){
            // make request and input
            $data = [];
            $response = Http::asForm()->post($url, $data);
        }elseif($template->endpoint->request == 'put'){
            $data = [];
            $response = Http::asForm()->put($url, $data);
        }else{
            $data = '';
            $userInput = preg_split('/\r\n|\r|\n/', $request->reply);
            // var_dump($userInput);
            foreach($template->endpoint->inputs as $ki => $input){
                if($ki > 0){
                    $data = $data.'&'.$input->name.'='.$userInput[$ki];
                }else{
                    $data = $data.'?'.$input->name.'='.$userInput[0];
                }
            }
            $url = $url.''.$data;
            // echo $url;
            $response = Http::asForm()->get($url);
        }
        // return 1;

        $trigger = Template::where('template_id', 1)->where('trigger', $response['code'])->first();
        // check respond code
        // find template anwser from api and check triger with code
        // $template = Template::where('template_id', $last->template_id)->where('trigger', $response['code'])->first();

        // if code 0
        if($trigger){
            foreach ($trigger->actions as $action) {
                if(!$action->is_multidata){
                    // return single data
                    $data = [];
                    $message = $action->message;
                    foreach ($action->data as $word) {
                        $strucure = explode(',', $word->value);
                        if(count($strucure)>0){
                            foreach ($strucure as $key => $st) {
                                if($key==0){
                                    $data[$word->name] = $response[$st];
                                }else{
                                    $data[$word->name] = $data[$word->name][$st];
                                }
                            }
                        }else{
                            $data[$word->name] = $response[$word->value];
                        }
                    }
                    $new = bind_to_template($data, $message);
                    // kirim message
                    echo $new;
                    // echo $response['data']['month'].' '.$response['data']['year'];
                }else{
                    $data = [];
                    $message = $action->message;

                    // find array data for looping
                    $structureLoop = explode(',', $action->array_data);
                    if(count($structureLoop)>0){
                        $dataLoop = [];
                        foreach ($structureLoop as $ley => $loop) {
                            if($ley==0){
                                $dataLoop = $response[$loop];
                            }else{
                                $dataLoop = $dataLoop[$loop];
                            }
                        }
                    }else{
                        $dataLoop = $response[$action->array_data];
                    }



                    foreach($dataLoop as $i => $item){
                        // echo $item['vesselName'].' '.$item['voyageNo'];
                        // echo ' (ETD : '.$item['etd'].' > '.$item['pol']. ' )';
                        // echo ' (ETA : '.$item['eta'].' > '.$item['pod']. ' )';
                        // echo ' ';
                        // echo '<br>';
                        // find data from data action
                        foreach ($action->data as $word) {
                            $strucure = explode(',', $word->value);
                            if(count($strucure)>0){
                                foreach ($strucure as $key => $st) {
                                    if($key==0){
                                        $data[$word->name] = $item[$st];
                                    }else{
                                        $data[$word->name] = $data[$word->name][$st];
                                    }
                                }
                            }else{
                                $data[$word->name] = $item[$word->value];
                            }
                        }

                        $new[$i] = bind_to_template($data, $message);
                    }
                    //kirim message
                    //foreach by array new
                    var_dump($new);
                }
            }
        }else{
            // check error
            if($template->error){
                // send error template if avaiable
                echo 'error template';
            }
            if($template->is_repeat_if_error){
                //sent repet question
                echo 'repet question/api';
            }
        }
        return '<br><br>Done';

        if($response['code']==0){
            return $response['msg'];
        }else{
            // if 1


        }



        return '<br><br>done';

        $message = "SITC SURABAYA 2109N
        (ETD)		                (ETA)
        Singapore	--    --	Qingdao
        2021-06-15                     2021-07-03

        SITC SURABAYA 2109S
        (ETD)		        (ETA)
        2021-06-02             2021-06-17
        Dalian	--    --	Port Klang";
    }


}
