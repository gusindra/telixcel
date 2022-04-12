<?php

namespace App\Observers;

use App\Models\Client;

class ClientObserver
{
    /**
     * Handle the Request "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function created(Client $request)
    {
        //
    }

    /**
     * Handle the Client "deleted" event.
     *
     * @param  \App\Client  $request
     * @return void
     */
    public function deleted(Client $request)
    {
        $team = $request->team;
        $request->teams()->detach($team);
    }
}


