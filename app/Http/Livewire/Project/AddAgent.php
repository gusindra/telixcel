<?php

namespace App\Http\Livewire\Project;

use App\Models\Notification;
use App\Models\Permission;
use App\Models\PermissionRole;
use App\Models\Project;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Team;
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

        $userId = (int) $this->selectedUser;
        $user = User::find($userId);
        $teamId = $this->project->team_id;

        // 1) Project membership (scoping).
        $this->project->members()->syncWithoutDetaching([$userId]);

        // 2) Team membership — required to reach team-scoped pages.
        $team = $teamId ? Team::find($teamId) : null;
        if ($team && ! $user->belongsToTeam($team)) {
            $team->users()->attach($userId, ['role' => 'editor']);
        }

        // 3) Ensure the user has an active role that can view projects in this team.
        $this->grantProjectAccess($userId, $teamId);

        // 4) Notify the invited user.
        if ($teamId) {
            Notification::create([
                'type'         => 'app',
                'notification' => 'You have been assigned to project "' . $this->project->name . '".',
                'user_id'      => $userId,
                'status'       => 'unread',
            ]);
        }

        $this->selectedUser = null;
        $this->emit('member_assigned');
    }

    /** Role ids that hold the VIEW PROJECT permission. */
    private function projectAccessRoleIds()
    {
        $permId = Permission::where('name', 'VIEW PROJECT')->value('id');
        if (! $permId) {
            return collect();
        }

        return PermissionRole::where('permission_id', $permId)->pluck('role_id');
    }

    /** Give the user an active role with project access in this team, unless they already have one. */
    private function grantProjectAccess($userId, $teamId): void
    {
        if (! $teamId) {
            return;
        }

        $accessRoleIds = $this->projectAccessRoleIds();

        $activeRoleIds = RoleUser::where('user_id', $userId)->where('team_id', $teamId)
            ->where('active', 1)->pluck('role_id');
        if ($activeRoleIds->intersect($accessRoleIds)->isNotEmpty()) {
            return; // already has project access
        }

        // Prefer a non Super Admin role that can view projects.
        $roleId = Role::whereIn('id', $accessRoleIds)
            ->orderByRaw("CASE WHEN name = 'Super Admin' THEN 1 ELSE 0 END")
            ->value('id');
        if (! $roleId) {
            return;
        }

        RoleUser::where('user_id', $userId)->where('team_id', $teamId)->update(['active' => 0]);
        RoleUser::updateOrCreate(
            ['user_id' => $userId, 'role_id' => $roleId, 'team_id' => $teamId],
            ['active' => 1, 'status' => 'active', 'working_id' => 'INV']
        );
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
