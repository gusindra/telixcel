<?php

namespace App\Http\Responses;

use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Support\Responsable;

class UpdateTeamResponse implements Responsable
{
    /**
     * @param  $request
     * @return mixed
     */
    public function toResponse($request)
    {
        // replace this with your own code
        // the user can be located with Auth facade
        Log::debug("test response curent team");
    }
}
