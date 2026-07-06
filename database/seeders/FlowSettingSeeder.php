<?php

namespace Database\Seeders;

use App\Models\FlowSetting;
use App\Models\Role;
use App\Models\RoleUser;
use App\Models\Team;
use Illuminate\Database\Seeder;

/**
 * Default approval flow: submitting a Project routes to an approver role per team, so the
 * Submit -> Approve flow works out of the box (no manual FlowSetting needed).
 */
class FlowSettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Team::all() as $team) {
            if (FlowSetting::where('model', 'PROJECT')->where('team_id', $team->id)->exists()) {
                continue;
            }

            $roleId = $this->approverRoleId($team);
            if (! $roleId) {
                continue;
            }

            FlowSetting::create([
                'model'         => 'PROJECT',
                'after_status'  => 'submit',
                'result_status' => 'approved',
                'role_id'       => $roleId,
                'team_id'       => $team->id,
                'description'   => 'Project approval',
            ]);
        }
    }

    /** Pick an approver role for the team — prefer Super Admin / Admin / Manager, else any role present. */
    private function approverRoleId(Team $team): ?int
    {
        $roleIds = RoleUser::where('team_id', $team->id)->pluck('role_id')->unique();
        $roles = Role::whereIn('id', $roleIds)->get();

        foreach (['Super Admin', 'Admin', 'Manager'] as $needle) {
            if ($match = $roles->first(fn ($r) => str_contains($r->name ?? '', $needle))) {
                return $match->id;
            }
        }

        return optional($roles->first())->id;
    }
}
