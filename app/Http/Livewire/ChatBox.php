<?php

namespace App\Http\Livewire;

use App\Models\Attachment;
use Livewire\Component;
use App\Models\Request;
use App\Models\Ticket;
use App\Models\Template;
use App\Models\Client;
use App\Models\HandlingSession;
use Illuminate\Support\Facades\Storage;
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
    public $link_attachment;
    public $request_id;
    public $reason;
    public $solution;
    public $ticket_id;
    public $ticket_status;
    public $ticket_reason;
    public $ticket_solution;
    public $ticket_type;
    public $quick_reply;
    public $forward_to;
    public $handling_session;
    public $modalAttachment = false;
    public $modalQuick = false;
    public $modalTicket = false;
    public $modalForward= false;
    public $modalUpdateTicket = false;
    public $transcript = 0;
    public $closeModal = false;
    public $session;

    public function mount($client_id)
    {
        if(!is_int($client_id)){
            @$client_id = Hashids::decode($client_id)[0];
        }
        $this->client = Client::find($client_id);
        $this->user_id = auth()->user()->id;
        $this->client_id = $client_id;
        $this->owner = auth()->user()->currentTeam->user_id;
        $this->quick_reply = Template::where('user_id', $this->owner)->where('type', 'helper')->get();
        $this->handling_session = $this->checkSession();
    }

    public function sendMessage(){
        // Check long of word if > will store to message
        Request::create([
            'reply'     => $this->message,
            'from'      => auth()->user()->id,
            'client_id' => $this->client->uuid,
            'user_id'   => $this->owner,
            'type'      => 'text'
        ]);

        $this->dispatchBrowserEvent('chat-send-message', [
            'from'      => $this->client->uuid,
            'from_web'  => strpos($this->client->phone, '@c.us') !== false ? $this->client->phone : $this->client->phone."@c.us",
            'user_id'   => $this->owner,
            'reply'     => $this->message,
        ]);

        $this->message = null;
        // dd(1);
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

            $this->dispatchBrowserEvent('chat-send-message', [
                'from'      => auth()->user()->id,
                'from_web'  => strpos($this->client->phone, '@c.us') !== false ? $this->client->phone : $this->client->phone."@c.us",
                'user_id'   => $this->owner,
                'reply'     => $this->message,
            ]);

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

    public function resetForm()
    {
        $this->message = null;
    }

    public function ticketShowModal($id, $message)
    {
        $this->request_id = $id;
        $this->reason = $message;
        $this->modalTicket = true;
    }

    public function sendTicket(){
        $ticket = Ticket::create([
            'reasons'       => $this->reason,
            'status'        => 'open',
            'request_id'    => $this->request_id,
            'created_by'    => auth()->user()->id
        ]);
        if($ticket){
            $this->modalTicket = false;
        }

        // Customer Service ACCOUNT akan memberikan informasi yang Bapak/Ibu butuhkan.
        // Sehubungan dengan pesan Bapak/Ibu, bahwa kami akan memproses permintaan dengan nomor tiket REQ00000028529

    }

    /**
     * ticketUpdateModal
     *
     * @param  mixed $ticket
     * @param  mixed $id
     * @param  mixed $reason
     * @param  mixed $status
     * @return void
     */
    public function ticketUpdateModal($ticket, $id, $reason, $status, $type = null)
    {
        $this->ticket_reason = $reason;
        $this->ticket_status = $status;
        $this->request_id = $ticket;
        $this->ticket_id = $id;
        $this->modalUpdateTicket = true;
        $this->ticket_type = $type;
    }

    /**
     * ticketUpdate
     *
     * @param  mixed $id
     * @param  mixed $reason
     * @param  mixed $status
     * @return void
     */
    public function ticketUpdate()
    {
        if($this->ticket_status!='close'){
            Ticket::find($this->ticket_id)->update([
                'solution'       => $this->ticket_solution,
                'status'        => $this->ticket_status,
                'updated_by'    => auth()->user()->id
            ]);
        }else{
            Ticket::find($this->ticket_id)->delete();
        }
        $this->modalUpdateTicket = false;
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

    /**
     * forwardShowModal
     *
     * @param  mixed $id
     * @param  mixed $message
     * @return void
     */
    public function forwardShowModal($id, $message)
    {
        $this->request_id = $id;
        $this->reason = $message;
        $this->modalForward = true;
    }

    public function sendForward(){

        $ticket = Ticket::create([
            'reasons'       => $this->reason,
            'status'        => 'waiting',
            'request_id'    => $this->request_id,
            'forward_to'    => $this->forward_to,
            'created_by'    => auth()->user()->id
        ]);
        if($ticket){
            $this->modalForward = false;
        }
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

    public function showModalConfirmation()
    {
        $this->closeModal = true;
    }

    public function closeChat($end)
    {
        if($this->checkSession()){
            $this->handling_session = HandlingSession::where('agent_id', $this->user_id)->delete();
            if($end){
                $lastRequest = Request::where('client_id', $this->client->uuid)->orderBy('created_at', 'DESC')->first();
                $lastRequest->is_closed = 1;
                $lastRequest->save();
            }
        }else{
            $this->emit('exist');
        }
        return redirect(request()->header('Referer'));
    }

    private function checkSession()
    {
        $session = HandlingSession::where('agent_id', $this->user_id)->first();
        if(!$session)
            $session = HandlingSession::where('client_id', $this->client_id)->where('user_id', $this->owner)->first();

        $this->session = $session;
        return $session;
    }

    public function showTransript()
    {
        $this->transcript = !$this->transcript;
    }

    public function updateTransript($selected){
        $status = $selected == 'yes' ? 'approve':'reject';
        $this->session->update(['view_transcript'=>$status]);
    }

    /**
     * The read function.
     *
     * @return void
     */
    public function read()
    {
        $data = [];
        if($this->client){
            if($this->transcript){
                $data['request'] = Request::with('client','agent')->where('client_id', $this->client->uuid)->get();
            }else{
                $closed = Request::where('client_id', $this->client->uuid)->where('is_closed', 1)->orderBy('id', 'desc')->first();
                $data['request'] = Request::with('client','agent')->where('client_id', $this->client->uuid)->where('id', '>=', $closed ? $closed->id : 0)->get();
            }
        }else{
            $data['request'] = [];
        }

        $data['count'] = count($data['request']);

        if(substr($this->message,0, 1)=='/'){
            $keyword = substr($this->message, 1);
            $data['quick'] = Template::where('user_id', $this->owner)->where('type', 'helper')->where('name','LIKE',"%{$keyword}%")->get();
        }else{
            $data['quick'] = [];
        }
        return $data;
    }

    public function render()
    {
        return view('livewire.chat-box', [
            'data' => $this->read(),
            'cid' => $this->client_id,
            'team' => auth()->user()->currentTeam,
            'quick_template' => $this->quick_reply->pluck('name','id'),
        ]);
    }
}
