<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Request;
use App\Models\Client;
use Livewire\WithFileUploads;

class ChatBox extends Component
{
    use WithFileUploads;

    public $client;
    public $message;
    public $user_id;
    public $client_id;
    public $clientId;
    public $owner;
    public $type;
    public $photo;
    public $modalAttachment = false;

    public function mount($client_id)
    {
        $this->client = Client::find($client_id);
        $this->user_id = auth()->user()->id;
        $this->client_id = $client_id;
        $this->owner = auth()->user()->currentTeam->user_id;

    }

    public function sendMessage(){
        Request::create([
            'reply'     => $this->message,
            'from'      => auth()->user()->id,
            'client_id' => $this->client->id,
            'user_id'   => $this->owner,
            'type'      => 'text'
        ]);
        $this->message = null;

        $this->dispatchBrowserEvent('chat-send-message', [
            'from'      => $this->client->id,
            'user_id'   => $this->owner,
            'reply'     => $this->message,
        ]);
        // dd(1);
    }

    public function sendAttachment(){
        Request::create([
            'reply'     => $this->message,
            'from'      => $this->client->id,
            'user_id'   => $this->owner,
            'type'      => $this->type
        ]);
        $this->message = null;

        $this->dispatchBrowserEvent('chat-send-message', [
            'from'      => $this->client->id,
            'user_id'   => $this->owner,
            'reply'     => $this->message,
        ]);
        // dd(1);
    }

    public function actionShowModal()
    {
        $this->modalAttachment = true;
        // $this->resetForm();
    }

    public function resetForm()
    {
        $this->message = null;
    }

    /**
     * The read function.
     *
     * @return void
     */
    public function read()
    {
        return Request::with('client','agent')->where('client_id', $this->client->id)->get();
    }

    public function render()
    {
        return view('livewire.chat-box', [
            'data' => $this->read(),
            'cid' => $this->client_id,
        ]);
    }
}
