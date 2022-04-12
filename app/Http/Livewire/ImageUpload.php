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

    public function save()
    {
        $this->validate([
            'photo' => 'image|max:1024',
        ]);

        $name ='1.'.$this->photo->extension();

        // $this->photo->storeAs('photos', $name);
        $file = Storage::disk('s3')->put('images', $this->photo);

        Attachment::create([
            'request_id'    => 1,
            'file'          => $file
        ]);

        session()->flash('message', 'The photo is successfully uploaded! ' );
    }

    public function render()
    {
        return view('livewire.image-upload', [
            'photos' => [],
        ]);
    }
}
