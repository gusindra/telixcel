<?php

namespace App\Http\Livewire;

use App\Models\Attachment;
use Livewire\Component;
use App\Models\Client;
use App\Models\HandlingSession;
use App\Models\Ticket;
use App\Models\Request;
use App\Models\Template;
use Illuminate\Support\Str;
use App\Models\WaWeb as ModelsWaWeb;
use Carbon\Carbon;
use Vinkla\Hashids\Facades\Hashids;
use Illuminate\Support\Facades\Storage;

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
    public $session;
    public $filter;
    public $search;
    public $modalTicket = false;
    public $request_id;
    public $ticket_status;
    public $ticket_reason;
    public $ticket_solution;
    public $handling_session = null;
    public $modalAttachment = false;
    public $link_attachment;
    public $type;
    public $photo;
    public $quick_reply;

    public function mount(){
        $this->user_id = auth()->user()->id;
        $this->owner = auth()->user()->currentTeam->user_id;
        if(auth()->user()->currentTeam->waWeb){
            $this->session = auth()->user()->currentTeam->waWeb->session;
        }
        $this->filter = "active";
        $this->search = "";
        $this->handling_session = $this->checkSession();
        $this->quick_reply = Template::where('user_id', $this->owner)->where('type', 'helper')->get();
    }

    /**
     * When user click list of client to starting live chat
     *
     * @param  mixed $id
     * @return void
     */
    public function chatCustomer($id){
        $id = Hashids::decode($id)[0];
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

    public function rules()
    {
        return [
            'client_name' => 'required',
        ];
    }

    public function modelData()
    {
        return [
            'name'  => $this->client_name,
            'note'  => $this->note,
            'tag'   => $this->tag
        ];
    }

    public function save()
    {
        $this->validate();
        $saved = Client::find($this->client_id)->update($this->modelData());
        $this->emit('saved');
    }

     /**
     * The read function.
     *
     * @return void
     */
    public function read()
    {
        $clients = auth()->user()->currentTeam->client;

        $sorted  = $clients->sortByDesc(function($client){
            return $client->date;
        });
        // dd($sorted->values()->all());

        if($this->filter=='waiting'){
            $template = auth()->user()->currentTeam->template;
            $wait = $template->filter(function($template){
                if($template->is_wait_for_chat==1){
                    return $template;
                }
            })->pluck(['id'])->toArray();

            $ticket = Ticket::leftJoin('requests', 'requests.id', '=', 'tickets.request_id')->select('requests.client_id', 'tickets.forward_to')->where('forward_to', auth()->user()->id)->get();
            //$ticket = Ticket::where('forward_to', auth()->user()->id)->get();
            $forward = $ticket->filter(function($ticket){
                return $ticket;
            })->pluck(['client_id'])->toArray();

            // dd($forward);
            $sorted = $sorted->filter(function($client) use ($wait, $forward){
                //template waiting || forward ticket
                if($client->newestRequest){
                    if(in_array($client->newestRequest->template_id, $wait) || in_array($client->uuid, $forward)){
                        return $client;
                    }elseif($client->newestRequest->created_at <= Carbon::now()->subMinutes(15)->toDateTimeString() && $client->active && !$client->newestRequest->is_closed){
                        return $client;
                    }
                }
            });
        }elseif($this->filter=='active'){
            $sorted  = $sorted->filter(function($client) {
                if($client->newestRequest){
                    if($client->newestRequest->from != 'bot' && $client->newestRequest->from != 'api'){
                        if($client->newestRequest->created_at >= Carbon::now()->subMinutes(15)->toDateTimeString() && !$client->newestRequest->is_closed){
                            return $client;
                        }elseif(@auth()->user()->chatsession->client_id==$client->id){
                            return $client;
                        }
                    }
                }
            });
        }

        if($this->search!=""){
            $sorted = $sorted->filter(function($client){
                if(str_contains(strtolower($client->name), strtolower($this->search))){
                    return $client;
                }
            });
        }

        return $sorted->values()->all();
    }

    public function saveResponse($details)
    {
        // dd($details);

        $client = $this->checkClient($details["from"].'_'.$details["to"],$details["from"],$details["from"],auth()->user()->currentTeam);

        $msg = Request::create([
            'source_id' => $details["from"].'_'.$details["to"],
            'reply'     => $details["body"],
            'from'      => $details["from"],
            'client_id' => $client->uuid,
            'user_id'   => $this->owner,
            'type'      => 'text'
        ]);

        if($msg && $this->session){
            // sent wa via wa web
            // need to get all response that create after this $msg only by bot and send to event lisener
            $replayed = Request::where('client_id', $msg->client_id)->where('from', "bot")->get();
            foreach($replayed as $reply){
                $this->dispatchBrowserEvent('chat-send-message', [
                    'from_web'  => strpos($client->phone, '@c.us') !== false ? $client->phone : $client->phone."@c.us",
                    'from'      => $client->uuid,
                    'user_id'   => $reply->user_id,
                    'reply'     => $reply->reply,
                ]);
            }
        }
        // Request::create([
        //     'source_id' => $data['source_id'],
        //     'reply'     => $data['reply'],
        //     'from'      => $data['client_id'],
        //     'user_id'   => $data['user_id'],
        //     'type'      => $data['type'],
        //     'client_id' => $data['client_uuid'],
        //     'sent_at'   => date('Y-m-d H:i:s'),
        // ]);
    }

    /**
     * The checker for client
     *
     * @return object
     */
    private function checkClient($id, $name, $from, $team)
    {
        $last_request = Request::with('client')->where('source_id', $id)->first();
        // Client::where('source_id', $message['id'])->where('from', $message['from'])->first();

        if($last_request){
            $client = $last_request->client;
        }else{
            // Need to find user base on credential
            $user_id = $this->user_id;

            $client = Client::create([
                'uuid'      => Str::uuid(),
                'sender'    => $name,
                'name'      => $name,
                'phone'     => $from,
                'user_id'   => $user_id
            ]);
            $client->teams()->attach($team);
        }

        return $client;
    }

    public function isFail()
    {
        $this->ready = false;
        ModelsWaWeb::where("team_id", auth()->user()->currentTeam->id)->update([
            'status' => 0
        ]);
    }

    public function sendMessage(){
        // Check long of word if > will store to message
        Request::create([
            'reply'     => $this->message,
            'from'      => auth()->user()->id,
            'client_id' => $this->client->uuid,
            'team_id'   => auth()->user()->currentTeam->id,
            'user_id'   => $this->owner,
            'type'      => 'text'
        ]);

        $this->message = null;
        $this->dispatchBrowserEvent('contentChanged', ['slide' => true]);

    }

    /**
     * joinChat
     *
     * @return void
     */
    public function joinChat()
    {
        if(!$this->checkSession()){
            $this->handling_session = HandlingSession::create([
                'client_id'     => $this->client_id,
                'agent_id'      => $this->user_id,
                'user_id'       => $this->owner,
            ]);
            Request::where('client_id', $this->client->uuid)->where('is_read', 0)->update(['is_read' => 1]);
        }else{
            if($this->handling_session->client_id == $this->client_id){
                $this->emit('handled');
            }else{
                $this->emit('exist');
            }
        }
    }

    private function checkSession()
    {
        $session = HandlingSession::where('agent_id', $this->user_id)->first();
        if(!$session)
            $session = HandlingSession::where('client_id', $this->client_id)->where('user_id', $this->owner)->first();

        return $session;
    }

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

    public function actionShowModal()
    {
        $this->modalAttachment = true;
    }

    public function quickReply(){
        $data = [];

        if(substr($this->message,0, 1)=='/'){
            $keyword = substr($this->message, 1);
            $data['quick'] = Template::where('user_id', $this->owner)->where('type', 'helper')->where('name','LIKE',"%{$keyword}%")->get();
        }else{
            $data['quick'] = [];
        }
        return $data;
    }

     /**
     * showQuickModal
     *
     * @param  mixed $id
     * @return void
     */
    public function showQuickModal($id)
    {
        $template = Template::find($id);
        $message = '';
        foreach($template->actions as $key => $action){
            if($key==0){
                $message = $action->message;
            }else{
                $message = $message.' '.$action->message;
            }
        }
        $this->message = $message;
    }

    public function getTicket()
    {
        $data = Ticket::with('request')->get();
        $sorted = $data->filter(function($ticket){
            if($ticket->request->team_id == auth()->user()->currentTeam->id){
                return $ticket;
            }
        });
        return $sorted->values()->all();

    }

    public function render()
    {
        return view('livewire.chat-component', [
            'data' => $this->read(),
            'tickets' => $this->getTicket(),
            'filter' => $this->filter,
            'handlingSession' => $this->checkSession(),
            'dataTemplate' => $this->quickReply(),
            'quick_template' => $this->quick_reply->pluck('name','id'),
        ]);
    }
}
