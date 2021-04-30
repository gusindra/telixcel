<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Request as Chat;
use App\Models\Client;
use Illuminate\Support\Str;

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
        $client_id = $client->id;
        $user_id = $client->user_id;
        //if chat type text
        $request = Chat::create([
            'source_id' => $data['message']['id'],
            'reply'     => $data['message']['text']['body'],
            'from'      => $client_id,
            'user_id'   => $user_id,
            'type'      => $data['message']['type'],
            'client_id' => $client_id,
            'sent_at'   => date('Y-m-d H:i:s', $data['message']['timestamp']),

        ]);

        return $request;
        return $data['message']['from'];
    }

    /**
     * The checker for client
     *
     * @return void
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
}
