<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Client;

class ChatComponent extends Component
{
    public $client;
    public $client_name;
    public $client_phone;
    public $note;
    public $tag;
    public $message;
    public $user_id;
    public $client_id;
    public $identity;
    public $owner;

    public function mount(){
        $this->user_id = auth()->user()->id;
        $this->owner = auth()->user()->currentTeam->user_id;
    }

    public function chatCustomer($id){
        $this->client_id = $id;
        $data = Client::find($this->client_id);
        $this->client = $data;
        $this->client_name = $data->name;
        $this->client_phone = $data->phone;
        $this->note = $data->note;
        $this->tag = $data->tag;
        $this->message = null;
        $this->emit('note.updated.' . $this->client_id);
        session(['key' => 'value']);
    }

    public function dispatchEvent(){
        $this->dispatchBrowserEvent('event-notification',[
            'eventName' => 'Sample Event',
            'eventMessage' => 'You have a sample notification'
        ]);
    }

     /**
     * The read function.
     *
     * @return void
     */
    public function read()
    {
        return Client::where('user_id', $this->owner)->get();
    }

    public function render()
    {
        return view('livewire.chat-component', [
            'data' => $this->read(),
        ]);
    }
}
