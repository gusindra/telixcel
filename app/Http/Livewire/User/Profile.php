<?php

namespace App\Http\Livewire\User;

use App\Models\BillingUser;
use App\Models\Client;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Team;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class Profile extends Component
{
    public $user;
    public $client;
    public $inputuser;
    public $inputclient;

    /** Selected role_id to set as the user's active role. */
    public $selectedRole;

    /** New password fields (admin reset). */
    public $password;
    public $password_confirmation;

    public function mount($user)
    {
        $this->user = User::find($user->id);

        $this->inputuser['name'] = $this->user->name ?? '';
        $this->inputuser['nick'] = $this->user->nick ?? '';
        $this->inputuser['email'] = $this->user->email ?? '';
        $this->inputuser['phone'] = $this->user->phone_no ?? '';

        $this->selectedRole = optional($this->user->activeRole)->role_id;
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

    /**
     * Set a new password for this user.
     * Allowed for Admin/Superadmin, or a user updating their own account.
     * Admin reset does not require the current password.
     */
    public function savePassword($id)
    {
        abort_unless($this->isAdmin() || auth()->id() === (int) $id, 403);

        $this->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::find($id);
        if (! $user) {
            return;
        }

        $user->update([
            'password' => Hash::make($this->password),
        ]);

        $this->password = '';
        $this->password_confirmation = '';
        $this->emit('password_saved');
    }

    /** Admin / superadmin gate (same pattern used across the app). */
    private function isAdmin(): bool
    {
        $auth = auth()->user();
        if (! $auth) {
            return false;
        }
        if ($auth->super->first()?->role === 'superadmin') {
            return true;
        }
        return $auth->activeRole && str_contains($auth->activeRole->role->name ?? '', 'Admin');
    }

    public function delete()
    {

    }

    /**
     * Assign / change the user's active role directly from the profile page.
     * Follows the same active-flag pattern as SwitchRole::updateRole():
     * clear all active flags for this user, then activate the chosen role.
     */
    public function saveRole()
    {
        $this->validate([
            'selectedRole' => 'required|exists:roles,id',
        ]);

        $teamId = $this->user->current_team_id;

        // Deactivate any currently active role for this user.
        RoleUser::where('user_id', $this->user->id)->update(['active' => null]);

        // Activate the selected role (create the pivot row if it doesn't exist).
        RoleUser::updateOrCreate(
            [
                'user_id' => $this->user->id,
                'role_id' => $this->selectedRole,
            ],
            [
                'team_id' => $teamId,
                'status'  => 'active',
                'active'  => 1,
            ]
        );

        $this->user = User::find($this->user->id);
        $this->emit('role_saved');
    }

    public function render()
    {
        return view('livewire.user.profile', [
            'roles' => Role::orderBy('name')->get(),
        ]);
    }
}
