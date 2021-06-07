<?php

namespace App\Observers;

use App\Models\Client;

class ClientObserver
{
    public $replyed = false;

    /**
     * Handle the Request "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function created(Client $request)
    {
        $team = auth()->user()->currentTeam;
        $request->teams()->attach($team);
    }
}


