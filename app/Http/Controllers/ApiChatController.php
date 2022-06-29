<?php

namespace App\Http\Controllers;

use App\Http\Resources\ChatResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Jobs\ProcessSmsApi;
use App\Models\ApiCredential;
use App\Models\Client;
use App\Models\Request as ModelsRequest;
use App\Models\Team;
use Vinkla\Hashids\Facades\Hashids;

class ApiChatController extends Controller
{

    /**
     * show record chat by phone number
     *
     * @param  mixed $phone
     * @return void
     */
    public function show($phone)
    {
        $customer = Client::where('phone', $phone)->where('user_id', auth()->user()->id)->first();
        if($customer){
            $data = ModelsRequest::where('user_id', '=', auth()->user()->id)->where('client_id', $customer->uuid)->get();

            return response()->json([
                'code' => 200,
                'message' => "Successful",
                'response' => ChatResource::collection($data),
            ]);
        }
        return response()->json([
            'code' => 404,
            'message' => "User not found"
        ]);
    }

    /**
     * post new chat
     *
     * @param  mixed $request
     * @return void
     */
    public function post(Request $request)
    {
        //get the request & validate parameters
        $fields = $request->validate([
            'slug' => 'required|string',
            'phone' => 'required|alpha_num',
            'text' => 'required|string',
        ]);

        try{
            $team = Team::where('user_id', auth()->user()->id)->where('slug', $request->slug)->first();
            $customer = Client::where('phone', $request->phone)->where('user_id', auth()->user()->id)->first();
            if($team && $customer){
                $request = ModelsRequest::create([
                    'source_id' => 'web_'.Hashids::encode($customer->id),
                    'reply'     => $request->text,
                    'from'      => $customer->id,
                    'user_id'   => auth()->user()->id,
                    'type'      => 'text',
                    'client_id' => $customer->uuid,
                    'team_id'   => $team->id,
                    'sent_at'   => date('Y-m-d H:i:s'),
                ]);
            }elseif(!$customer){
                return response()->json([
                    'message' => "Phone Number Not Found",
                    'code' => 400
                ]);
            }else{
                return response()->json([
                    'message' => "Team Chat Not Found",
                    'code' => 400
                ]);
            }
        }catch(\Exception $e){
            return response()->json([
                'message' => "Invalid input",
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
     * send boardcast chat
     *
     * @param  mixed $request
     * @return void
     */
    public function sendBulk(Request $request)
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
}
