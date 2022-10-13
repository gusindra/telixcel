<?php

namespace App\Observers;

use App\Jobs\SendMessageViaApi;
use App\Models\Request as Message;
use App\Models\Template;
use App\Models\Team;
use App\Models\ApiCredential;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RequestObserver
{
    public $replyed = false;

    /**
     * Handle the Request "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function created(Message $request)
    {
        $team = $request->client->team ? $request->client->team->detail : false;
        if($request->team_id>0){
            $team = Team::find($request->team_id);
        }
        if($team){
            //Log::debug($request->reply);
            $request->teams()->attach($team);

            //Check if request from customer
            if($request->source_id)
            {

                // check if has first time template
                $count = checkFirstRequest($request);
                if($count == 1)
                {
                    //send welcome
                    $template = Template::where('type', 'welcome')->where('user_id', $request->user_id)->with('teams')
                        ->whereHas('teams', function ($query) use($request) {
                            $query->where([
                                'teams.id' => $request->team_id
                            ]);
                        })->get();
                    // Log::debug('gelooo');
                    // Log::debug($template);

                    if($template){
                        foreach($template as $trigger){
                            if($trigger->actions->count()>0){
                                foreach($trigger->actions as $action){
                                    //if chat type text
                                    $this->addRespond($action, $request, $trigger, true);
                                }
                            }
                        }
                    }
                }

                //check if any template sent before
                $last = getPreviousRequest($request);
                if($last && $last->id)
                {
                    //check if template if question
                    if($last->template_id)
                    {
                        // check last template
                        $template = Template::find($last->template_id);
                        if($template->type==='api'){

                            // call event job to request to API Partner
                            $url = $template->endpoint->endpoint;
                            if($template->endpoint->request == 'post'){
                                // make request and input
                                $data = '';
                                $userInput = preg_split('/\r\n|\r|\n/', $request->reply);
                                // var_dump($userInput);
                                foreach($template->endpoint->inputs as $ki => $input){
                                    if($ki > 0){
                                        if (array_key_exists($ki,$userInput)){
                                            $data = $data.'&'.$input->name.'='.$userInput[$ki];
                                        }else{
                                            $data = $data.'&'.$input->name.'=';
                                        }
                                    }else{
                                        $data = $data.'?'.$input->name.'='.$userInput[0];
                                    }
                                }
                                $url = $url.''.$data;
                                $response = Http::asForm()->post($url);
                            }elseif($template->endpoint->request == 'put'){
                                $data = '';
                                $userInput = preg_split('/\r\n|\r|\n/', $request->reply);
                                // var_dump($userInput);
                                foreach($template->endpoint->inputs as $ki => $input){
                                    if($ki > 0){
                                        if (array_key_exists($ki,$userInput)){
                                            $data = $data.'&'.$input->name.'='.$userInput[$ki];
                                        }else{
                                            $data = $data.'&'.$input->name.'=';
                                        }
                                    }else{
                                        $data = $data.'?'.$input->name.'='.$userInput[0];
                                    }
                                }
                                $url = $url.''.$data;
                                $response = Http::asForm()->put($url, $data);
                            }else{
                                $data = '';
                                $userInput = preg_split('/\r\n|\r|\n/', $request->reply);
                                // var_dump($userInput);
                                foreach($template->endpoint->inputs as $ki => $input){
                                    if($input->value!=null){
                                        $data = $data.'&'.$input->name.'='.$input->value;
                                    }elseif($ki > 0){
                                        if (array_key_exists($ki,$userInput)){
                                            $data = $data.'&'.$input->name.'='.$userInput[$ki];
                                        }else{
                                            $data = $data.'&'.$input->name.'=';
                                        }
                                    }else{
                                        $data = $data.'?'.$input->name.'='.$userInput[0];
                                    }
                                }
                                $url = $url.''.$data;
                                // echo $url;
                                $response = Http::asForm()->get($url);
                            }
                            // make logic to check template from api
                            Log::debug($url);
                            //Log::debug($response);
                            $trigger = Template::where('template_id', $template->id)->where('trigger', $response['code'])->first();
                            if($trigger){
                                foreach ($trigger->actions as $action) {
                                    if(!$action->is_multidata){
                                        Log::debug("check single data");
                                        // return single data
                                        $data = [];
                                        $message = $action->message;
                                        foreach ($action->data as $word) {
                                            $strucure = explode(',', $word->value);
                                            if(count($strucure)>0){
                                                foreach ($strucure as $key => $st) {
                                                    if($st){
                                                        if($key==0){
                                                            $data[$word->name] = $response[$st];
                                                        }else{
                                                            $data[$word->name] = $data[$word->name][$st];
                                                        }
                                                    }
                                                }
                                            }else{
                                                $data[$word->name] = $response[$word->value];
                                            }
                                        }
                                        $new = bind_to_template($data, $message);
                                        // kirim message
                                        // echo $new;
                                        // echo $response['data']['month'].' '.$response['data']['year'];
                                        $this->sendRespondApi($new, $request, $trigger);
                                    }else{
                                        Log::debug("check multi data");
                                        $new = [];
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
                                            foreach ($action->data as $word) {
                                                $strucure = explode(',', $word->value);
                                                if(count($strucure)>0){
                                                    Log::debug("Name Word :".$word->name);
                                                    foreach ($strucure as $key => $st) {
                                                        Log::debug("Strucure :".$st);
                                                        if($key==0 && $word->name){
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
                                        // var_dump($new);
                                        if(count($new)>0){
                                            foreach ($new as $msg) {
                                                $this->sendRespondApi($msg, $request, $trigger);
                                            }
                                        }else{
                                            $msg = 'Empty data';
                                            $this->sendRespondApi($msg, $request, $trigger);
                                        }
                                    }
                                }
                            }else{
                                // check error
                                $this->sendErrorRespond($request, $template);
                            }
                        }elseif($template->type==='question' || $template->type==='error'){
                            // check the awnser base on trigger
                            $trigger = Template::where('template_id', $last->template_id)->where('trigger', $request->reply)->first();
                            if($trigger){
                                if($trigger->actions->count()>0){
                                    foreach($trigger->actions as $action){
                                        //if chat type text
                                        $this->addRespond($action, $request, $trigger);
                                    }
                                }
                            }else{
                                $this->sendErrorRespond($request, $template);
                            }
                        }
                    }
                }

                if(!$this->replyed){
                    //check if trigger contain reply if not a question
                    //Log::debug('trigger contain reply if not a question '.$request->reply);
                    $template = Template::where('is_enabled', 1)->whereNull('template_id')->where('trigger_condition', 'like', '%equal%')->where('trigger', $request->reply)->with('teams')
                        ->whereHas('teams', function ($query) use($request) {
                            $query->where([
                                'teams.id' => $request->team_id
                            ]);
                        })->first();
                    // Log::debug($template);
                    if(!$template){
                        $contains = Template::where('is_enabled', 1)->whereNull('template_id')->where('trigger_condition', 'contain')->whereHas('teams', function ($query) use($request) {
                            $query->where([
                                'teams.id' => $request->team_id
                            ]);
                        })->pluck('trigger','id');

                        foreach($contains as $key => $contain) {
                            Log::debug($contain);
                            $place = strpos($request->reply, $contain);
                            if (!empty($place)) {
                                $template = Template::find($key);
                            }
                        }

                        // $template = Template::where('is_enabled', 1)->whereNull('template_id')->where('trigger_condition', 'contain')->where('trigger', 'like', '%' . $request->reply . '%')->where('user_id', $request->user_id)->with('teams')
                        //             ->whereHas('teams', function ($query) use($request) {
                        //                 $query->where([
                        //                     'teams.id' => $request->team_id
                        //                 ]);
                        //             })->first();

                        Log::debug($template);
                    }
                    if($template){
                        if($template->actions->count()>0)
                        {
                            foreach($template->actions as $action){
                                //if chat type text
                                $this->addRespond($action, $request, $template);
                            }
                        }
                        Log::debug('done sending template ');
                    }else{
                        Log::debug('no response template ');
                    }
                }
            }
            else
            {
                if($request->template_id==null){
                    $this->sendToWhatsapp($request);
                }
            }
        }
    }

    /**
     * add respond from bot template
     *
     * @param  mixed $action
     * @param  mixed $request
     * @param  mixed $template
     * @return void
     */
    private function addRespond($action, $request, $template, $welcome=false)
    {
        // Create respond from template
        // text
        if($action->type=='text'){
            $request = Message::create([
                'reply'         => $action->message,
                'from'          => 'bot',
                'user_id'       => $request->user_id,
                'type'          => 'text',
                'template_id'   => $template->id,
                'client_id'     => $request->client_id
            ]);
        }else{
            $request = Message::create([
                'reply'         => '',
                'media'         => $action->message,
                'from'          => 'bot',
                'user_id'       => $request->user_id,
                'type'          => $action->type,
                'template_id'   => $template->id,
                'client_id'     => $request->client_id
            ]);
        }
        $request->setAttribute('welcome', $welcome);
        $this->sendToWhatsapp($request);

        $this->replyed = true;
    }

    /**
     * add respond from api client
     *
     * @param  mixed $message
     * @param  mixed $request
     * @param  mixed $template
     * @return void
     */
    private function sendRespondApi($message, $request, $template, $welcome=false)
    {
        // Create respond from template
        $request = Message::create([
            'reply'         => $message,
            'from'          => 'api',
            'user_id'       => $request->user_id,
            'type'          => 'text',
            'template_id'   => $template->id,
            'client_id'     => $request->client_id
        ]);
        $request->setAttribute('welcome', $welcome);
        $this->sendToWhatsapp($request);

        $this->replyed = true;
    }

    /**
     * sendToWhatsapp
     *
     * @return void
     */
    private function sendToWhatsapp($request){
        $userCredention = ApiCredential::where("user_id", $request->user_id)->where("is_enabled", 1)->first();
        // check if has token apicredential
        if($userCredention){
            // Log::debug("1 client". auth()->user()->currentTeam->apiCredential);
            // foreach($userCredention as $key => $api){
            //     if($api->is_enabled == 1){
                    //Log::debug($request." ".$api);
                    //Log::debug($key."----".$api);
            //     }
            // }
            SendMessageViaApi::dispatch($request, $userCredention);
        }elseif(auth()->user() && auth()->user()->currentTeam && auth()->user()->currentTeam->waWeb ){
            // jika tikda ada api alternatif menggunakan wa web

        }
    }

    /**
     * add respond error to customer
     *
     * @param  mixed $request
     * @param  mixed $template
     * @return void
     */
    private function sendErrorRespond($request, $template)
    {
        // send error template if avaiable
        if($template->error){
            if($template->error->actions->count()>0){
                foreach($template->error->actions as $error){
                    //if chat type text
                    $this->addRespond($error, $request, $template->error);
                }
            }
        }

        //sent repet question
        if($template->is_repeat_if_error == 1){
            if($template->actions->count()>0){
                foreach($template->actions as $action){
                    //if chat type text
                    $this->addRespond($action, $request, $template);
                }
            }
        }
    }

    /**
     * Handle the Message "deleted" event.
     *
     * @param  \App\Message  $request
     * @return void
     */
    public function deleted(Message $request)
    {
        $team = auth()->user()->currentTeam;
        $request->teams()->detach($team);
    }
}
