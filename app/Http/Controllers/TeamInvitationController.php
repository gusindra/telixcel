<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TeamInvitation;
use App\Models\TeamUser;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class TeamInvitationController extends Controller
{
    /**
     * Accept a team invitation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RoleInvitation  $invitation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function accept(Request $request, TeamInvitation $invitation)
    {
        $newTeamMember = User::where('email', $invitation->email)->first();
        if($newTeamMember){
            $roles = TeamUser::where('user_id', $newTeamMember->id)->get();
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

            $teamUser = TeamUser::firstOrCreate(
                ['user_id' =>  $newTeamMember->id, 'team_id' => $invitation->team_id],
                ['role' => $invitation->role]
            );

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
    public function destroy(Request $request, TeamInvitation $invitation)
    {
        if (! Gate::forUser($request->user())->check('removeTeamMember', $invitation->team)) {
            throw new AuthorizationException;
        }

        $invitation->delete();

        return back(303);
    }
}
