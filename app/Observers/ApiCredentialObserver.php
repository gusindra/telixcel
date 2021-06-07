<?php

namespace App\Observers;

use App\Models\ApiCredential;

class ApiCredentialObserver
{
    public $replyed = false;

    /**
     * Handle the Request "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function created(ApiCredential $request)
    {
        $team = $request->team;
        $request->teams()->attach($team);
    }
}


