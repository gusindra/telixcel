<?php

namespace App\Http\Livewire\User;

use App\Models\BillingUser;
use App\Models\Client;
use App\Models\Team;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;

class Profile extends Component
{
    public $user;
    public $client;
    public $inputuser;
    public $inputclient;

    public function mount($user)
    {
        $this->user = User::find($user->id);

        $this->inputuser['name'] = $this->user->name ?? '';
        $this->inputuser['nick'] = $this->user->nick ?? '';
        $this->inputuser['email'] = $this->user->email ?? '';
        $this->inputuser['phone'] = $this->user->phone_no ?? '';
        // dd($this->inputuser);
        if($this->user->isClient){
            $this->inputclient['title'] = $this->user->isClient->title ?? '';
            $this->inputclient['name'] = $this->user->isClient->name ?? '';
            $this->inputclient['phone'] = $this->user->isClient->phone ?? '';
            $this->inputclient['address'] = $this->user->isClient->address ?? '';
            $this->inputclient['notes'] = $this->user->isClient->note ?? '';

            $this->inputclient['name'] = $this->user->userBilling->name ?? '';
            $this->inputclient['postcode'] = $this->user->userBilling->post_code ?? '';
            $this->inputclient['province'] = $this->user->userBilling->province ?? '';
            $this->inputclient['city'] = $this->user->userBilling->city ?? '';
            $this->inputclient['tax_id'] = $this->user->userBilling->tax_id ?? '';
            $this->inputclient['address'] = $this->user->userBilling->address ?? '';
        }
    }

    public function saveUser($id)
    {
        // dd($id);
        $user = User::find($id);
        if($this->user->isClient && $user->email != $this->inputuser['email']){
            $this->user->isClient->update([
                'email' => $this->inputuser['email']
            ]);
        }
        $user->update([
            'name'      => $this->inputuser['name'],
            'phone_no'  => $this->inputuser['phone'],
            'email'     => $this->inputuser['email'],
            'nick'      => $this->inputuser['nick']
        ]);
        $this->emit('user_saved');
    }

    public function saveClient()
    {
        // dd($this->user);
        if($this->user->isClient){
            $this->user->isClient->update([
                'title'     => $this->inputclient['title'],
                'name'      => $this->inputclient['name'],
                'phone'     => $this->inputclient['phone'],
                'address'   => $this->inputclient['address'],
                'note'      => $this->inputclient['notes'],
            ]);
            $this->user->userBilling->update([
                'tax_id'        => $this->inputclient['tax_id'],
                'name'          => $this->inputclient['name'],
                'post_code'     => $this->inputclient['postcode'],
                'address'       => $this->inputclient['address'],
                'province'      => $this->inputclient['province'],
                'city'          => $this->inputclient['city'],
            ]);
        }else{
            $customer =  Client::create([
                'title'     => $this->inputclient['title'],
                'name'      => $this->inputclient['name'],
                'phone'     => $this->inputclient['phone'],
                'address'   => $this->inputclient['address'],
                'note'      => $this->inputclient['notes'],
                'email'     => $this->user->email,
                'user_id'   => 0,
                'uuid'      => Str::uuid()
            ]);
            $team = Team::find(0);
            $customer->teams()->attach($team);
            if($customer){
                $billing = BillingUser::create([
                    'tax_id'        => $this->inputclient['tax_id'],
                    'name'          => $this->inputclient['name'],
                    'post_code'     => $this->inputclient['postcode'],
                    'address'       => $this->inputclient['address'],
                    'province'      => $this->inputclient['province'],
                    'city'          => $this->inputclient['city'],
                    'user_id'       => $this->user->id
                ]);
            }
        }
        $this->emit('client_saved');
    }

    public function delete()
    {

    }

    public function render()
    {
        return view('livewire.user.profile');
    }
}
