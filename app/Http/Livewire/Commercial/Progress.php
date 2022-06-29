<?php

namespace App\Http\Livewire\Commercial;

use App\Models\FlowProcess;
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

        $this->emit('saved');
    }

    public function next($status=''){
        $update_status = 'approved';
        if($status==''){
            $update_status = $status;
        }
        $this->model->update([
            'status' => $update_status
        ]);

        $flow = FlowProcess::find($this->model->approval->id);

        $flow->user_id = auth()->user()->id;
        $flow->status = $update_status;
        $flow->comment = $this->remark;

        $flow->save();

        $this->approval = false;

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

        $this->emit('saved');
    }

    public function render()
    {
        return view('livewire.commercial.progress', [
            'approvals' => $this->read()
        ]);
    }
}
