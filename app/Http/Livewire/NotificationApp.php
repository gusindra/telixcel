<?php

namespace App\Http\Livewire;

use App\Models\Notification;
use Livewire\Component;

class NotificationApp extends Component
{
    public $client_id;

    /**
     * mount
     *
     * @param  mixed $client_id
     * @return void
     */
    public function mount($client_id)
    {
        $this->client = $client_id;
    }

    public function waiting()
    {
        $clients = auth()->user()->currentTeam->client;

        $sorted  = $clients->sortByDesc(function($client){
            return $client->date;
        });

        $template = auth()->user()->currentTeam->template;
        $wait = $template->filter(function($template){
            if($template->is_wait_for_chat==1){
                return $template;
            }
        })->pluck(['id'])->toArray();

        $sorted  = $sorted->filter(function($client) use ($wait){
            //template waiting || forward ticket
            if(in_array($client->newestRequest->template_id, $wait)){
                return $client;
            }
        });

        return $sorted->values()->all();

    }

    /**
     * The read function.
     *
     * @return void
     */
    public function read()
    {
        //$data = [];
        $data['waiting'] = $this->waiting();
        $data['notif'] = Notification::where('user_id', $this->client_id)->orderBy('id', 'desc')->take(8)->get();
        $data['count'] = Notification::where('user_id', $this->client_id)->where('status', 'new')->count() + count($data['waiting']);

        return $data;
    }

    public function render()
    {   
        return view('livewire.notification-app', [
            'data' => $this->read(),
        ]);
    }
}
