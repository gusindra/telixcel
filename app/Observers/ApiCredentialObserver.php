<?php

namespace App\Observers;

use App\Models\ApiCredential;
use Illuminate\Support\Facades\Log;

class ApiCredentialObserver
{
    /**
     * Handle the ApiCredential "created" event.
     *
     * @param  \App\Models\ApiCredential  $request
     * @return void
     */
    public function created(ApiCredential $request)
    {
        $team = auth()->user()->currentTeam;
        $request->teams()->attach($team);
    }

    /**
     * Handle the ApiCredential "created" event.
     *
     * @param  \App\Models\ApiCredential  $request
     * @return void
     */
    public function updated(ApiCredential $request)
    {
        if($request->is_enabled == 1){
            ApiCredential::where('id', '!=', $request->id)->where('user_id', $request->user_id)->where('is_enabled', 1)->update(['is_enabled' => 0]);
        }
    }

    /**
     * Handle the ApiCredential "deleted" event.
     *
     * @param  \App\ApiCredential  $audio
     * @return void
     */
    public function deleted(ApiCredential $request)
    {
        $team = auth()->user()->currentTeam;
        $request->teams()->detach($team);
    }
}


