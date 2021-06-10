<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client as TwilioAPI;
use MessageBird\Client as MessageBirdAPI;
use MessageBird\Objects\Conversation\Content;
use MessageBird\Objects\Conversation\Message;
use MessageBird\Objects\Conversation\HSM\Message as HSMMessage;
use MessageBird\Objects\Conversation\HSM\Params as HSMParams;
use MessageBird\Objects\Conversation\HSM\Language as HSMLanguage;


class SendMessageViaApi implements ShouldQueue
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
        //check active API client
        if($this->service->client == 'twilio'){
            Log::debug("ready sent to WA from ".$this->service->client);
            $this->twillioApi();
        }else if($this->service->client == 'messagebird'){
            Log::debug("ready sent to WA from ".$this->service->client);
            $this->messageBird();
        }
    }

    private function messageBird(){
        $api = $this->service->api_key;//'p08wRA7EzyFJmeIxZL9niloLU'
        // $messageBird = new MessageBirdAPI($api); // Set your own API access key here.
        // // Enable the whatsapp sandbox feature
        // //$messageBird = new \MessageBird\Client(
        // //    'p08wRA7EzyFJmeIxZL9niloLU',
        // //    null,
        // //    [\MessageBird\Client::ENABLE_CONVERSATIONSAPI_WHATSAPP_SANDBOX]
        // //);
        // $conversationId = $this->service->server_key;//'2e15efafec384e1c82e9842075e87beb';
        // $content = new Content();
        // $content->text = '';
        // $message = new Message();
        // $message->channelId = $this->service->credential;//'2bf28721b9a84d8395494dfee02332cd';
        // $message->type = $this->request->reply;
        // $message->content = $content;

        // try {
        //     $conversation = $messageBird->conversationMessages->create($conversationId, $message);

        //     Log::debug('trying');
        //     Log::debug($conversation);
        // } catch (\Exception $e) {
        //     Log::debug('error');
        //     Log::debug($e->getMessage());
        //     // echo sprintf("%s: %s", get_class($e), $e->getMessage());
        // }

        //$messageBird = new MessageBirdAPI('p08wRA7EzyFJmeIxZL9niloLU');
        // Enable the whatsapp sandbox feature
        $messageBird = new MessageBirdAPI(
           'p08wRA7EzyFJmeIxZL9niloLU',
           null,
           [MessageBirdAPI::ENABLE_CONVERSATIONSAPI_WHATSAPP_SANDBOX]
        );
        $content = new Content();
        $hsm = new HSMMessage();
        $hsmParamsName = new HSMParams();
        $hsmParamsName->default = 'Bob';
        $hsmParamsWhen = new HSMParams();
        $hsmParamsWhen->default = 'Tommorrow!';
        $hsmLanguage = new HSMLanguage();
        $hsmLanguage->policy = HSMLanguage::DETERMINISTIC_POLICY;
        //$hsmLanguage->policy = HSMLanguage::FALLBACK_POLICY;
        $hsmLanguage->code = 'YOUR LANGUAGE CODE';
        $hsm->templateName = 'support';
        $hsm->namespace = 'f51df27d_49c1_4cdd_8667_0337bd506e81';
        $hsm->params = array($hsmParamsName, $hsmParamsWhen);
        $hsm->language = $hsmLanguage;
        $content->hsm = $hsm;
        $message = new Message();
        $message->channelId = '2bf28721b9a84d8395494dfee02332cd';
        $message->content = $content;
        $message->to = '6281339668556';
        $message->type = 'hsm';
        try {
            Log::debug('trying');
            $conversation = $messageBird->conversations->start($message);
            //Log::debug($conversation);
            // var_dump($conversation);
        } catch (\Exception $e) {
            Log::debug('error');
            Log::debug($e->getMessage());
        }
    }

    private function twillioApi(){
        $sid    = $this->service->api_key;//"AC6c598c40bbbb22a9c3cb76fd7baa67b8";
        $token  = $this->service->server_key;//"500107131bbdb25dee1992053e93409f";
        $send_to = "whatsapp:+".$this->request->client->phone; //"whatsapp:+6281339668556"

        $twilio = new TwilioAPI($sid, $token);

        $message = $twilio->messages
                        ->create($send_to, // to
                                array(
                                    "from" => $this->service->credential,//"whatsapp:+14155238886",
                                    "body" => $this->request->reply
                                )
                        );
        Log::debug($message);
    }
}
