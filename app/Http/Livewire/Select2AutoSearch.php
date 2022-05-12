<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Select2AutoSearch extends Component
{
    public $viralSongs = '';

    public $songs = [
        'Say So',
        'The Box',
        'Laxed',
        'Savage',
        'Dance Monkey',
        'Viral',
        'Hotline Billing',
    ];

    public function render()
    {
        return view('livewire.select2-auto-search');
    }
}
