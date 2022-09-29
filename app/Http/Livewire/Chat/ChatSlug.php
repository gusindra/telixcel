<?php

namespace App\Http\Livewire\Chat;

use App\Models\Attachment;
use Livewire\Component;
use App\Models\Client;
use App\Models\Request;
use App\Models\Team;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Storage;

class ChatSlug extends Component
{
    use WithFileUploads;

    public $name;
    public $number;
    public $client;
    public $team;
    public $message;
    public $user_id;
    public $client_id;
    public $clientId;
    public $owner;
    public $type;
    public $photo;
    public $modalAttachment = false;
    public $link_attachment;

    public function mount($team)
    {
        $this->team = $team;
    }

    /**
     * checkClient
     *
     * @return void
     */
    public function checkClient()
    {
        // checking client
        $client = Client::where('phone',$this->number)->where('user_id', $this->team->user_id)->first();
        // if not exsist create new client
        if(!$client){
            $client = Client::create([
                'uuid'          => Str::uuid(),
                'name'          => $this->name,
                'sender'        => $this->name,
                'phone'         => $this->number,
                'user_id'       => $this->team->user_id,
            ]);
            $team = auth()->user()->currentTeam;
            $client->teams()->attach($team);
        }
        $this->client = $client;
        $this->client_id = $client->id;
        $this->owner =  $this->client->user_id;
    }

    /**
     * sendMessage
     *
     * @return void
     */
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
            'team_id'   => $this->team->id
        ]);
        $this->message = null;
        // dd($request->client->team->detail);
        $this->dispatchBrowserEvent('chat-send-message', [
            'from'      => $this->client->uuid,
            'user_id'   => $this->owner,
            'reply'     => $this->message,
        ]);
    }

    /**
     * sendAttachment
     *
     * @return void
     */
    /**
     * sendAttachment
     * image : jpg, jpeg, png
     * audio : aac, mp4, amr, mpeg, ogg
     * video : mp4, 3gp
     * document : pdf, word, powerpoint, excel, txt, valid Mime
     * stickers : webp
     *
     * @return void
     */
    public function sendAttachment(){
        if($this->photo){
            $this->validate([
                'photo' => 'image|max:1024',
            ]);

            $file = Storage::disk('s3')->put('images', $this->photo);

            $this->link_attachment = 'https://telixcel.s3.ap-southeast-1.amazonaws.com/'.$file;
            $this->type = 'image';
        }else{
            $this->type = attachmentExt($this->link_attachment);
        }

        if($this->type){
            $request = Request::create([
                'reply'     => $this->message,
                'media'     => $this->link_attachment,
                'from'      => auth()->user()->id,
                'user_id'   => $this->owner,
                'client_id' => $this->client->uuid,
                'type'      => $this->type
            ]);
            $this->message = null;
            $this->modalAttachment = false;

            Attachment::create([
                'request_id'    => $request->id,
                'file'          => $this->link_attachment
            ]);
        }else{
            dd('Format link false');
        }
    }

    /**
     * Get user chat
     *
     * @return void
     */
    public function read(){
        if($this->client)
            return $this->client;
        return null;
    }

    /**
     * Get msg user
     *
     * @return void
     */
    public function message()
    {
        if($this->client){
            return Request::with('client','agent')->where('client_id', $this->client->uuid)->get();
        }
        return [];
    }

    public function render()
    {
        return view('livewire.chat.chat-slug', [
            'data' => $this->read(),
            'messages' => $this->message(),
        ]);
    }
}
