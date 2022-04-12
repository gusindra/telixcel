<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Client;
use App\Models\Ticket;
use App\Models\Request;
use Illuminate\Support\Str;
use App\Models\WaWeb as ModelsWaWeb;
use Carbon\Carbon;
use Vinkla\Hashids\Facades\Hashids;

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

    public function mount(){
        $this->user_id = auth()->user()->id;
        $this->owner = auth()->user()->currentTeam->user_id;
        if(auth()->user()->currentTeam->waWeb){
            $this->session = auth()->user()->currentTeam->waWeb->session;
        }
        $this->filter = "active";
        $this->search = "";
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

        // update handling agent
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
                if(in_array($client->newestRequest->template_id, $wait) || in_array($client->uuid, $forward)){
                    return $client;
                }elseif($client->newestRequest->created_at <= Carbon::now()->subMinutes(15)->toDateTimeString() && $client->active && !$client->newestRequest->is_closed){
                    return $client;
                }
            });
        }elseif($this->filter=='active'){
            $sorted  = $sorted->filter(function($client) {
                if($client->newestRequest->from != 'bot' && $client->newestRequest->from != 'api'){
                    if($client->newestRequest->created_at >= Carbon::now()->subMinutes(15)->toDateTimeString() && !$client->newestRequest->is_closed){
                        return $client;
                    }elseif(@auth()->user()->chatsession->client_id==$client->id){
                        return $client;
                    }
                }
            });
        }

        if($this->search!=""){
            $sorted  = $sorted->filter(function($client){
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

    public function render()
    {
        return view('livewire.chat-component', [
            'data' => $this->read(),
            'filter' => $this->filter,
        ]);
    }
}
