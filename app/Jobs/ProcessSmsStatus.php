<?php

namespace App\Jobs;

use App\Models\BlastMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSmsStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(array_key_exists('msgID',$this->request)){
            BlastMessage::where("msg_id", $this->request['msgID'])->where("msisdn", $this->request['msisdn'])->first()->update([
                'status' => $this->request['status']
            ]);
        }elseif(array_key_exists('msgid',$this->request)){
            BlastMessage::where("msg_id", $this->request['msgid'])->where("msisdn", $this->request['msisdn'])->first()->update([
                'status' => $this->request['status']
            ]);
        }else{
            Log::debug($this->request);
        }
    }
}
