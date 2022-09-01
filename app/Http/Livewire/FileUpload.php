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
    public $deleteConfirmation = false;
    public $deleteSelection;
    public $uploadAble = true;

    public function mount($model, $model_id, $status = null)
    {
        $this->model = $model;
        $this->model_id = $model_id;
        if($status){
            $this->status = $status;
        }
        $count = Attachment::where('model', $model)->where('model_id',$model_id)->count();
        if($this->model=='contract' ){
            if($count>=1 && $this->status=='draft'){
                $this->uploadAble = false;
            }elseif($count>=2 && $this->status=='submit'){
                $this->uploadAble = false;
            }
        }elseif($this->model=='order' || $this->model=='invoice'){
            if($this->status=='paid'){
                $this->uploadAble = false;
            }
        }elseif($this->status=='approved'){
            $this->uploadAble = false;
        }else{
            $this->uploadAble = !disableInput($status);
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
     * delete confirmation
     *
     * @return void
     */
    public function actionConfirmation($id)
    {
        $this->deleteSelection = $id;
        $this->deleteConfirmation = true;
    }
    /**
     * delete file
     *
     * @return void
     */
    public function remove(){
        $attch = Attachment::find($this->deleteSelection);
        Storage::disk('s3')->delete($attch->file);
        $attch->delete();
        $this->deleteSelection = null;
        $this->emit('deleted');
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
