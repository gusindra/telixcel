<?php

namespace App\Http\Responses;

use App\Models\TeamUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    /**
     * @param  $request
     * @return mixed
     */
    public function toResponse($request)
    {
        // replace this with your own code
        // the user can be located with Auth facade

        $home = Auth::user()->is_admin ? config('fortify.dashboard') : config('fortify.home');

        $teamuser = TeamUser::where('team_id', empty(auth()->user()->currentTeam)?1:auth()->user()->currentTeam->id)->where('user_id', auth()->user()->id)->first();

        if($teamuser){
            $teamuser->update([
                'status' => 'Online'
            ]);

            
        }

        return $request->wantsJson()
            ? response()->json(['two_factor' => false])
            : redirect($home);
    }
}
