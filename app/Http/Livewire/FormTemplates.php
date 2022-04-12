<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Template;

class FormTemplates extends Component
{
    public $modalActionVisible = false;
    public $templateType = null;
    /**
     * createShowModal
     *
     * @return void
     */
    public function actionShowModal()
    {
        $this->modalActionVisible = true;
    }

    public function createTemplate()
    {
        return 1;
    }

    public function render()
    {
        return view('livewire.form-templates');
    }
}
