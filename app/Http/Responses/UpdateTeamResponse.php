<?php

namespace App\Http\Responses;

use App\Models\RoleUser;
use App\Models\TeamUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Jetstream\Contracts\CurentTeamResponse;

class UpdateTeamResponse implements CurentTeamResponse
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
