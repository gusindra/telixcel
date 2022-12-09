<?php

namespace App\Http\Livewire\Project;

use App\Models\FlowProcess;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Progress extends Component
{
    public $project;
    public $projectId;
    public $approval = false;
    public $remark;
    public $theme;

    public function mount($uuid)
    {
        $this->projectId = $uuid;
        $this->project = Project::find($uuid);
    }

    public function read()
    {
        return FlowProcess::where('model', 'PROJECT')->where('model_id', $this->projectId)->get();
    }

    public function submit(){
        $this->project->update([
            'status' => 'submit'
        ]);
        return redirect(request()->header('Referer'));

        $this->emit('saved');
    }

    public function next(){
        $this->project->update([
            'status' => 'approved'
        ]);

        $flow = FlowProcess::find($this->project->approval->id);

        $flow->user_id = auth()->user()->id;
        $flow->status = 'approved';
        $flow->comment = $this->remark;

        $flow->save();
        return redirect(request()->header('Referer'));

        $this->emit('saved');
    }

    public function decline(){
        $this->project->update([
            'status' => 'draft'
        ]);

        $flow = FlowProcess::find($this->project->approval->id);

        $flow->user_id = auth()->user()->id;
        $flow->status = 'decline';
        $flow->comment = $this->remark;

        $flow->save();
        return redirect(request()->header('Referer'));

        $this->emit('saved');
    }

    public function render()
    {
        if($this->theme==1){
            return view('livewire.project.theme.progress', [
                'approvals' => $this->read()
            ]);
        }
        return view('livewire.project.progress', [
            'approvals' => $this->read()
        ]);
    }
}
