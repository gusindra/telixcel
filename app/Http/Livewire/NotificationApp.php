<?php

namespace App\Http\Livewire;

use App\Models\Notification;
use Livewire\Component;

class NotificationApp extends Component
{
    public $client_id;
    public $modalActionVisible = false;
    public $currentMessage;

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
        if(auth()->user()->currentTeam && auth()->user()->currentTeam->client){
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
                //dd($client->newestRequest);
                if($client->newestRequest && @$client->newestRequest->template_id){
                    if(in_array($client->newestRequest->template_id, $wait)){
                        return $client;
                    }
                }
            });

            return $sorted->values()->all();
        }else{
            return [];
        }

    }

    /**
     * createShowModal
     *
     * @return void
     */
    public function actionShowModal($id)
    {
        $notif = Notification::find($id);
        $this->currentMessage = $notif->notification;
        $this->modalActionVisible = true;
        $notif->status = 'read';
        $notif->save();
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
        $data['notif'] = Notification::where('user_id', $this->client_id)->orderBy('id', 'desc')->take(15)->get();
        $data['count'] = Notification::where('user_id', $this->client_id)->whereIn('status', ['new','unread'])->count() + count($data['waiting']);

        $this->dispatchBrowserEvent('event-notification',[
            'eventName' => 'Sound Alert',
            'eventMessage' => $data['count']
        ]);
        return $data;
    }

    public function render()
    {
        return view('livewire.notification-app', [
            'data' => $this->read(),
        ]);
    }
}
