<?php

namespace App\Http\Livewire\Saldo;

use App\Models\SaldoUser;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Topup extends Component
{
    public $modalActionVisible = false;
    public $team;
    public $currency;
    public $amount;
    public $description;
    public $user;
    public $mutation;

    public function rules()
    {
        return [
            'amount' => 'required',
            'currency' => 'required',
            'description' => 'required',
        ];
    }

    public function create()
    {
        $this->validate();
        SaldoUser::create($this->modelData());
        $this->modalActionVisible = false;
        $this->resetForm();
        $this->emit('refreshLivewireDatatable');
    }

    public function modelData()
    {
        $data = [
            'team_id'       => $this->team,
            'currency'      => $this->currency,
            'amount'        => $this->amount,
            'mutation'      => $this->mutation,
            'description'   => $this->description,
            'user_id'       => $this->user->id,
        ];
        return $data;
    }

    public function resetForm()
    {
        $this->team_id = null;
        $this->currency = null;
        $this->amount = null;
        $this->mutation = null;
        $this->description = null;
    }

    public function actionShowModal()
    {
        $this->modalActionVisible = true;
    }

    public function render()
    {
        return view('livewire.saldo.topup');
    }
}
