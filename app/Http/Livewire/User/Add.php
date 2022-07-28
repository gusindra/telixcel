<?php

namespace App\Http\Livewire\User;

use App\Models\Team;
use App\Models\User;
use Laravel\Jetstream\Jetstream;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Jetstream\Events\AddingTeam;

class Add extends Component
{
    public $modalActionVisible = false;
    public $input;
    public $entity;
    public $model;
    public $source;

    public function rules()
    {
        return [
            'input.name'        => 'required',
            'input.name'        => 'required',
            'input.password'    => 'required',
        ];
    }

    public function create()
    {
        $this->validate();

        $user = User::create($this->modelData());
        $team = Team::find(1);
        $newTeamMember = Jetstream::findUserByEmailOrFail($user->email);
        $team->users()->attach(
            $newTeamMember, ['role' => 'editor']
        );
        AddingTeam::dispatch($user);
        $user->switchTeam($team = $user->ownedTeams()->create([
            'name' => $user->name,
            'slug' => slugify($user->name),
            'personal_team' => false,
        ]));

        $this->modalActionVisible = false;
        $this->resetForm();
        $this->emit('refreshLivewireDatatable');
    }

    public function generatePassword()
    {
        $this->input['password'] = Str::random(8);
    }

    public function modelData()
    {
        $data = [
            'name' => $this->input['name'],
            'email' => $this->input['email'],
            'password' => Hash::make($this->input['password']),
        ];
        return $data;
    }

    public function resetForm()
    {
        $this->input = null;
    }

    /**
     * createShowModal
     *
     * @return void
     */
    public function actionShowModal()
    {
        $this->modalActionVisible = true;
    }

    public function render()
    {
        return view('livewire.user.add');
    }
}
