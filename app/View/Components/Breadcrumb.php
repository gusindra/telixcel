<?php

namespace App\View\Components;

use App\Support\Breadcrumbs;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public $items;

    public function __construct($items = null)
    {
        $this->items = $items ?: Breadcrumbs::items();
    }

    public function render()
    {
        return view('components.breadcrumb');
    }
}
