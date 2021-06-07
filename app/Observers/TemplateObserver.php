<?php

namespace App\Observers;

use App\Models\Template;

class TemplateObserver
{
    public $replyed = false;

    /**
     * Handle the Request "created" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function created(Template $request)
    {
        $team = auth()->user()->currentTeam;
        $request->teams()->attach($team);
    }
}


