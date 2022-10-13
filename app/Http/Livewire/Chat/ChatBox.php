<?php

namespace App\Http\Livewire\Chat;

use Livewire\Component;
use App\Models\Request;
use App\Models\Client;
use Livewire\WithFileUploads;
use Vinkla\Hashids\Facades\Hashids;

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
    public $team;
    public $transcript = false;
    public $modalAttachment = false;

    public function mount($client_id=null)
    {
        if(!is_null($client_id)){
            $this->client = Client::find($client_id);
            $this->client_id = $client_id;
            $this->owner =  $this->client->user_id;
            $this->team =  $this->client->team->detail;
        }
    }

    public function sendMessage(){
        // Check long of word if > will store to message
        $request = Request::create([
            'source_id' => 'web_'.Hashids::encode($this->client->id),
            'reply'     => $this->message,
            'from'      => $this->client->id,
            'user_id'   => $this->owner,
            'type'      => 'text',
            'client_id' => $this->client->uuid,
            'sent_at'   => date('Y-m-d H:i:s'),
        ]);
        $this->message = null;
        // dd($request->client->team->detail);
        $this->dispatchBrowserEvent('chat-send-message', [
            'from'      => $this->client->uuid,
            'user_id'   => $this->owner,
            'reply'     => $this->message,
        ]);
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
    }

    public function actionShowModal()
    {
        $this->modalAttachment = true;
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
        if($this->client && $this->transcript){
            return Request::with('client','agent')->where('client_id', $this->client->uuid)->get();
        }
        return [];
    }

    public function render()
    {
        return view('livewire.chat.chat-box', [
            'data' => $this->read(),
            'cid' => $this->client_id
        ]);
    }
}
