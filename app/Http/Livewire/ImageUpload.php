<?php

namespace App\Http\Livewire;

use App\Models\Attachment;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ImageUpload extends Component
{
    use WithFileUploads;

    public $photo;
    public $model;
    public $model_id;

    public function mount($model, $model_id)
    {
        $this->model = $model;
        $this->model_id = $model_id;
    }

    public function save()
    {
        $this->validate([
            'photo' => 'image|max:1024',
        ]);

        $name = date('YmdHis');'.'.$this->photo->extension();
        $path = 'images/'.date('Y').'/'.date('F');

        // $this->photo->storeAs('photos', $name);
        $file = Storage::disk('s3')->put($path, $this->photo);

        Attachment::create([
            'model'         => $this->model,
            'model_id'      => $this->model_id,
            'uploaded_by'   => auth()->user()->id,
            'request_id'    => null,
            'file'          => $file
        ]);

        session()->flash('message', 'The photo is successfully uploaded! ' );
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
        return view('livewire.image-upload', [
            'photos' => $this->read(),
        ]);
    }
}
