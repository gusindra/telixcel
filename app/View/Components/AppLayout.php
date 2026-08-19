<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // layouts.app      = sidebar kiri (sekarang)
        // layouts.app_old  = Jetstream top-nav (lama)
        return view('layouts.app');
    }
}
