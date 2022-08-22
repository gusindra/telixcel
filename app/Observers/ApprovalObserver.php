<?php

namespace App\Observers;

use App\Models\FlowProcess;
use App\Models\Notification;
use App\Models\RoleUser;
use Illuminate\Support\Facades\Log;

class ApprovalObserver
{
    /**
     * Handle the FlowProcess "created" event.
     *
     * @param  \App\Models\FlowProcess  $request
     * @return void
     */
    public function created(FlowProcess $request)
    {
        $users = RoleUser::where('team_id', auth()->user()->current_team_id)->where('role_id', $request->role_id)->get();
        foreach($users as $user){
            Notification::create([
                'type' => 'app',
                'model' => 'FlowProcess',
                'model_id' => $request->id,
                'notification' => $request->model. ' ID: '.$request->model_id. ' Task '. $request->task .' '. $request->status,
                'user_id' => $user->user_id,
                'status' => 'unread'
            ]);
        }
        // Log::debug('create approval'. $request->id);
    }

    /**
     * Handle the FlowProcess "created" event.
     *
     * @param  \App\Models\FlowProcess  $request
     * @return void
     */
    public function updated(FlowProcess $request)
    {
        //check if status is not released
        if($request->status=='released' || $request->status=='reviewed' || $request->status=='approved'){
            $checkFlow = FlowProcess::where('model', $request->model)->where('model_id', $request->model_id)->whereNull('status')->count();
            if($checkFlow==0){
                $users = FlowProcess::where('model', $request->model)->where('model_id', $request->model_id)->groupBy('user_id')->get();
                foreach($users as $user){
                    Notification::create([
                        'type' => 'app',
                        'model' => 'FlowProcess',
                        'model_id' => $request->id,
                        'notification' => $request->model. ' ID: '.$request->model_id. ' Task '. $request->task .' '. $request->status,
                        'user_id' => $user->user_id,
                        'status' => 'unread'
                    ]);
                }
            }
        }else{
            $flow = FlowProcess::whereNull('status')->where('model', $request->model)->where('model', $request->model_id)->orderBy('id', 'ASC')->first();
            if($flow){
                $users = RoleUser::where('team_id', auth()->user()->current_team_id)->where('role_id', $flow->role_id)->get();
                foreach($users as $user){
                    Notification::create([
                        'type' => 'app',
                        'model' => 'FlowProcess',
                        'model_id' => $flow->id,
                        'notification' => $flow->model. ' ID: '.$flow->model_id. ' Task '. $request->task .' is '. $request->status,
                        'user_id' => $user->user_id,
                        'status' => 'unread'
                    ]);
                }
            }
        }
        // Log::debug('update approval', $request);
    }

    /**
     * Handle the FlowProcess "deleted" event.
     *
     * @param  \App\Model\FlowProcess  $request
     * @return void
     */
    public function deleted(FlowProcess $request)
    {
        $flows = FlowProcess::whereNull('status')->where('model', $request->model)->where('model', $request->model_id)->get();
        foreach($flows as $flow){
            Notification::where('model', 'FlowProcess')->where('model_id', $flow->id)->delete();
        }
        // Log::debug('delete approval', $request);
    }
}


