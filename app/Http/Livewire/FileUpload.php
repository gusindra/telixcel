<?php

namespace App\Http\Livewire;

use App\Models\Attachment;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class FileUpload extends Component
{
    use WithFileUploads;

    public $input_file;
    public $model;
    public $model_id;
    public $status;

    public function mount($model, $model_id, $status = null)
    {
        $this->model = $model;
        $this->model_id = $model_id;
        if($status){
            $this->status = $status;
        }
    }

    public function save()
    {
        $this->validate([
            'input_file' => 'file|max:2048',
        ]);

        $name = date('YmdHis');'.'.$this->input_file->extension();
        $path = 'document/'.date('Y').'/'.date('F');

        // $this->photo->storeAs('photos', $name);
        $file = Storage::disk('s3')->put($path, $this->input_file);

        Attachment::create([
            'model'         => $this->model,
            'model_id'      => $this->model_id,
            'uploaded_by'   => auth()->user()->id,
            'request_id'    => null,
            'file'          => $file
        ]);

        session()->flash('message', 'The file is successfully uploaded! ' );
    }

    /**
     * The read function.
     *
     * @return void
     */
    public function read()
    {
        $data = Attachment::where('model', $this->model)->where('model_id', $this->model_id)->get();
        return $data;
    }


    public function render()
    {
        return view('livewire.file-upload', [
            'files' => $this->read(),
        ]);
    }
}
