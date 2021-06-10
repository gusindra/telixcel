<?php

namespace App\Observers;

use App\Models\Template;

class TemplateObserver
{
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

    /**
     * Handle the Template "deleted" event.
     *
     * @param  \App\Template  $request
     * @return void
     */
    public function deleted(Template $request)
    {
        $team = auth()->user()->currentTeam;
        $request->teams()->detach($team);
    }
}


