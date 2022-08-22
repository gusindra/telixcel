<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Theme extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.theme');
    }

    protected $listeners = ['ModeView'];
    public $mode;

    public function ModeView($Color)
    {
        $this->mode = $Color;
    }
    public function SwitchMode($SwitcMode)
    {
        // you need to add the switch somewhere in your code to be able to toggle the mode
        $this->mode = $SwitcMode;
        $newMode = $SwitcMode == 'dark' ? 'light' : 'dark';
        $this->dispatchBrowserEvent('view-mode', ['newMode' => $newMode]);
    }
}
