<?php

namespace App\Http\Livewire\Commercial;

use App\Models\Billing;
use App\Models\Commision;
use App\Models\Contract;
use App\Models\FlowProcess;
use App\Models\FlowSetting;
use App\Models\Order;
use App\Models\Project;
use App\Models\Quotation;
use Livewire\Component;

class Progress extends Component
{
    public $model;
    public $model_type;
    public $model_id;
    public $approval = false;
    public $remark;

    public function mount($model, $id)
    {
        $this->model_type = $model;
        $this->model_id = $id;
        if($model=='project'){
            $this->model = Project::find($id);
        }elseif($model=='quotation'){
            $this->model = Quotation::find($id);
        }elseif($model=='contract'){
            $this->model = Contract::find($id);
        }elseif($model=='order'){
            $this->model = Order::find($id);
        }elseif($model=='commission'){
            $this->model = Commision::find($id);
        }elseif($model=='invoice'){
            $this->model = Billing::find($id);
        }
    }

    public function read()
    {
        return FlowProcess::where('model', $this->model_type)->where('model_id', $this->model_id)->get();
    }

    public function submit(){
        $this->model->update([
            'status' => 'submit'
        ]);

        return redirect(request()->header('Referer'));
        $this->emit('saved');
    }

    public function next($status=''){
        $update_status = $status;
        $flow = FlowProcess::find($this->model->approval->id);
        $setting = FlowSetting::where('description', $flow->task)->where('role_id', $flow->role_id)->first();
        if($setting){
            $update_status = $setting->result_status;
        }
        // dd($update_status);
        $this->model->update([
            'status' => $update_status
        ]);

        $flow->user_id = auth()->user()->id;
        $flow->status = $update_status;
        $flow->comment = $this->remark;

        $flow->save();

        $this->approval = false;

        return redirect(request()->header('Referer'));
        $this->emit('saved');


    }

    public function decline(){
        $this->model->update([
            'status' => 'draft'
        ]);

        $flow = FlowProcess::find($this->model->approval->id);

        $flow->user_id = auth()->user()->id;
        $flow->status = 'decline';
        $flow->comment = $this->remark;

        $flow->save();

        FlowProcess::where('model', $flow->model)->where('model_id', $flow->model_id)->whereNull('status')->delete();

        return redirect(request()->header('Referer'));
        $this->emit('saved');
    }

    public function revise(){
        $this->model->update([
            'status' => 'draft'
        ]);
        $flow = FlowProcess::find($this->model->approval->id);
        FlowProcess::where('model', $flow->model)->where('model_id', $flow->model_id)->delete();
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.commercial.progress', [
            'approvals' => $this->read()
        ]);
    }
}
