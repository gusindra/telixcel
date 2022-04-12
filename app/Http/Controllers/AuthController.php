<?php

namespace App\Http\Controllers;

use App\Http\Responses\LogoutResponse;
use App\Models\TeamUser;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\StatefulGuard;

class AuthController extends Controller
{
    /**
     * The guard implementation.
     *
     * @var \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected $guard;

    /**
     * Create a new controller instance.
     *
     * @param  \Illuminate\Contracts\Auth\StatefulGuard  $guard
     * @return void
     */
    public function __construct(StatefulGuard $guard)
    {
        $this->guard = $guard;
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Fortify\Contracts\LogoutResponse
     */
    public function destroy(Request $request)
    {
        // ->where('team_id',auth()->user()->currentTeam->id)
        TeamUser::where('user_id',auth()->user()->id)->update([
            'status' => NULL
        ]);

        $this->guard->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return app(LogoutResponse::class);
    }
}
