<?php

namespace App\Http\Livewire\Role;

use App\Mail\RoleInvitation as MailRoleInvitation;
use App\Models\RoleInvitation;
use App\Models\RoleUser;
use Livewire\Component;
use Illuminate\Support\Facades\Mail;

class Member extends Component
{
    public $team;
    public $role;
    public $inviteEmail;
    public $inviteCancel;
    public $confirmingTeamMemberRemoval = false;

    public function mount($id)
    {
        $this->role = $id;
    }

    public function addRoleMember()
    {
        $invitation = RoleInvitation::create([
            'email' => $this->inviteEmail,
            'role_id' => $this->role,
            'team_id' => auth()->user()->current_team_id
        ]);

        Mail::to($this->inviteEmail)->send(new MailRoleInvitation($invitation));

        $this->resetForm();

        $this->emit('saved');
    }

    public function resetForm()
    {
        $this->inviteEmail = null;
    }

    public function cancelTeamInvitation($id)
    {
        $this->inviteCancel = $id;
        $this->confirmingTeamMemberRemoval = true;
    }

    public function removeTeamMember()
    {
        RoleInvitation::find($this->inviteCancel)->delete();
        $this->confirmingTeamMemberRemoval = false;
        $this->inviteCancel = null;
        $this->emit('deleted');
    }

    public function readInvite()
    {
        return RoleInvitation::where('role_id', $this->role)->get();
    }

    public function readUser()
    {
        return RoleUser::where('role_id', $this->role)->get();
    }

    public function render()
    {
        return view('livewire.role.member', [
            'invites' => $this->readInvite(),
            'users' => $this->readUser()
        ]);
    }
}
