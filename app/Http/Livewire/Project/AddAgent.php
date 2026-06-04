<?php

namespace App\Http\Livewire\Project;

use App\Models\Project;
use App\Models\User;
use Livewire\Component;

class AddAgent extends Component
{
    public $project_id;
    public $project;
    public $selectedUser; // user id to assign (existing user only)

    // unassign confirmation
    public $confirmingUnassign = false;
    public $unassignId = null;
    public $unassignName = '';

    public function mount($id)
    {
        $this->project_id = $id;
        $this->project = Project::find($id);
    }

    /**
     * Assign an existing user to the project (does not create users).
     */
    public function assign()
    {
        $this->validate(['selectedUser' => 'required|exists:users,id']);
        $this->project->members()->syncWithoutDetaching([$this->selectedUser]);
        $this->selectedUser = null;
        $this->emit('member_assigned');
    }

    public function confirmUnassign($userId)
    {
        $user = User::find($userId);
        $this->unassignId = $userId;
        $this->unassignName = $user?->name ?? '';
        $this->confirmingUnassign = true;
    }

    public function unassign()
    {
        if ($this->unassignId) {
            $this->project->members()->detach($this->unassignId);
        }
        $this->confirmingUnassign = false;
        $this->unassignId = null;
        $this->emit('member_assigned');
    }

    private function availableUsers()
    {
        $assigned = $this->project->members()->pluck('users.id')->all();

        return User::whereNotIn('id', $assigned)
            ->where('name', '!=', 'Admin')
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.project.add-agent', [
            'members'        => $this->project->members()->get(),
            'availableUsers' => $this->availableUsers(),
        ]);
    }
}
