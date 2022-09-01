<?php

use App\Console\Commands\ProjectAssistance;
use App\Models\Contract;
use App\Models\TeamUser;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('reset-login', function () {
    $teamUser = TeamUser::where('status', '!=', NULL)->where('updated_at', '<', Carbon::now()->subHours(2))->update([
        'status' => NULL
    ]);
    if(!$teamUser){
        Log::debug("reset fail");
        $this->comment('reset fail');
    }else{
        Log::debug("reset done");
        $this->comment('reset done');
    }
})->purpose('Reset user status who is not logout');


// Artisan::command('assistance:project', ProjectAssistance::class)->purpose('Display an expired project');
