<?php

namespace App\Observers;

use App\Models\Quotation;
use App\Models\FlowProcess;
use Illuminate\Support\Facades\Auth;

class QuotationObserver
{
    /**
     * Handle the Project "updated" event.
     *
     * @param  \App\Models\Request  $request
     * @return void
     */
    public function updated(Quotation $request)
    {
        if($request->status == 'submit')
        {
            FlowProcess::create([
                'model'     => 'quotation',
                'model_id'  => $request->id,
                'user_id'   => Auth::user()->id,
                'status'    => 'submited'
            ]);

            FlowProcess::create([
                'model'     => 'quotation',
                'model_id'  => $request->id,
                'role_id'   => 1,
                'task'      => '1st Approver'
            ]);
        }

        if($request->status == 'approved')
        {
            //Releaser
            FlowProcess::create([
                'model'     => 'quotation',
                'model_id'  => $request->id,
                'role_id'   => 1,
                'task'      => 'Releasor'
            ]);
        }
    }
}
