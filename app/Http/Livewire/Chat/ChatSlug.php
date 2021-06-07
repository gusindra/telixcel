<?php

namespace App\Http\Livewire\Chat;

use Livewire\Component;
use App\Models\Client;
use App\Models\Team;
use Illuminate\Support\Str;

class ChatSlug extends Component
{
    public $name;
    public $number;
    public $client;
    public $team;

    public function mount($team)
    {
        $this->team = $team;
    }

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
        }
        $this->client = $client;
    }

    public function render()
    {
        return view('livewire.chat.chat-slug');
    }
}
