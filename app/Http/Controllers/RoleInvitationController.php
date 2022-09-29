<?php

namespace App\Http\Controllers;

use App\Models\RoleInvitation;
use App\Models\RoleUser;
use App\Models\User;
use App\Models\TeamUser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class RoleInvitationController extends Controller
{
    /**
     * Accept a team invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RoleInvitation  $invitation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accept(Request $request, RoleInvitation $invitation)
    {
        $newTeamMember = User::where('email', $invitation->email)->first();
        if($newTeamMember){
            $roles = RoleUser::where('user_id', $newTeamMember->id)->get();
            //CHECK IF HAVE SAME ROLE NAME IN SAME TEAM
            if(count($roles)>0){
                foreach($roles as $role){
                    if($role->role_id == $invitation->role_id && $role->team_id == $invitation->team_id){
                        return redirect('login')->banner(
                            __('Sorry! You already have this role in the same team.')
                        );
                    }
                }
            }

            $roleUser = RoleUser::where('user_id', $newTeamMember->id)->get();
            if($roleUser){
                RoleUser::where('user_id', $newTeamMember->id)->update(['active'=>NULL]);
            }

            RoleUser::create([
                'user_id' => $newTeamMember->id,
                'role_id' => $invitation->role_id,
                'team_id' => $invitation->team_id
            ]);

            $teamUser = TeamUser::firstOrCreate(
                ['user_id' =>  $newTeamMember->id],
                ['team_id' => $invitation->team_id],
                ['role' => 'member']
            );

            // TeamUser::create([
            //     'user_id'   => $newTeamMember->id,
            //     'role'      => 'member',
            //     'team_id'   => $invitation->team_id
            // ]);

            $invitation->delete();

            $newTeamMember->update(['current_team_id'=>$invitation->team_id]);

            return redirect('login')->banner(
                __('Great! You have accepted the invitation to join Telixcel team.')
            );
        }
        return redirect('register?email='.$invitation->email)->banner(
            __('Opss! Before accepted the invitation to please signup first to continue login.')
        );
    }

    /**
     * Cancel the given team invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RoleInvitation  $invitation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, RoleInvitation $invitation)
    {
        if (! Gate::forUser($request->user())->check('removeTeamMember', $invitation->team)) {
            throw new AuthorizationException;
        }

        $invitation->delete();

        return back(303);
    }
}
