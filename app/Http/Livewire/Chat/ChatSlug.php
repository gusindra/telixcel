<?php

namespace App\Http\Livewire\Chat;

use App\Models\Attachment;
use Livewire\Component;
use App\Models\Client;
use App\Models\HandlingSession;
use App\Models\Request;
use App\Models\Team;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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
    public $transcript = false;
    public $requestTransript;

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
            $team = Team::find($this->team->id);
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

        $this->dispatchBrowserEvent('contentChanged', ['newName' => $request->id]);
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
                'source_id' => 'web_'.Hashids::encode($this->client->id),
                'reply'     => $this->message,
                'media'     => $this->link_attachment,
                'from'      => $this->client->id,
                'user_id'   => $this->owner,
                'client_id' => $this->client->uuid,
                'type'      => $this->type,
                'sent_at'   => date('Y-m-d H:i:s'),
                'team_id'   => $this->team->id
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
     * Request to see transcript
     *
     * @return void
     */
    public function requestTransript(){
        $session = HandlingSession::where('client_id', $this->client->id)->where('user_id', $this->owner)->first();
        // dd($session);
        if($session){
            if(is_null($session->view_transcript)){
                $session->view_transcript = 'requested';
                $session->save();
                $this->requestTransript = 'requested';
            }else{
                $this->requestTransript = $session->view_transcript;
            }
        }else{
            $this->transcript = false;
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
            if($this->transcript){
                return Request::with('client','agent')->where('client_id', $this->client->uuid)->get();
            }else{
                return Request::with('client','agent')->where('client_id', $this->client->uuid)->whereDate('created_at', Carbon::today())->get();
            }
        }
        return [];
    }

    public function actionShowModal()
    {
        $this->modalAttachment = true;
    }

    public function render()
    {
        return view('livewire.chat.chat-slug', [
            'data' => $this->read(),
            'messages' => $this->message(),
        ]);
    }
}
