<?php

namespace App\Observers;

use App\Models\Request as Message;
use App\Models\Template;
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
        //Check if request from customer
        if($request->source_id)
        {
            // check if has first time template
            $count = checkFirstRequest($request);
            if($count == 1)
            {
                //send welcome
                $template = Template::where('type', 'welcome')->where('user_id', $request->user_id)->get();
                if($template){
                    foreach($template as $trigger){
                        if($trigger->actions->count()>0){
                            foreach($trigger->actions as $action){
                                //if chat type text
                                $this->addRespond($action, $request, $trigger);
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
                            // echo $url;
                            $response = Http::asForm()->get($url);
                        }
                        // make logic to check template from api
                        Log::debug($url);
                        Log::debug($response);
                        $trigger = Template::where('template_id', 1)->where('trigger', $response['code'])->first();
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
                $template = Template::where('is_enabled', 1)->whereNull('template_id')->where('user_id', $request->user_id)->where('trigger', $request->reply)->first();
                if($template){
                    if($template->actions->count()>0)
                    {
                        foreach($template->actions as $action){
                            //if chat type text
                            $this->addRespond($action, $request, $template);
                        }
                    }
                }
            }
        }
        else
        {
            $this->sendToWhatsapp($request);
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
    private function addRespond($action, $request, $template)
    {
        // Create respond from template
        $request = Message::create([
            'reply'         => $action->message,
            'from'          => 'bot',
            'user_id'       => $request->user_id,
            'type'          => 'text',
            'template_id'   => $template->id,
            'client_id'     => $request->client_id
        ]);
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
    private function sendRespondApi($message, $request, $template)
    {
        // Create respond from template
        $request = Message::create([
            'reply'         => $message,
            'from'          => 'bot',
            'user_id'       => $request->user_id,
            'type'          => 'text',
            'template_id'   => $template->id,
            'client_id'     => $request->client_id
        ]);
        $this->sendToWhatsapp($request);

        $this->replyed = true;
    }

    /**
     * sendToWhatsapp
     *
     * @return void
     */
    private function sendToWhatsapp($request){
        Log::debug("ready sent to WA: ". $request);
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
}
