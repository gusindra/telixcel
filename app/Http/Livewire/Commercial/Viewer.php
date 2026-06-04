<?php

namespace App\Http\Livewire\Commercial;

use App\Models\Contract;
use App\Models\Quotation;
use Livewire\Component;

/**
 * Lightweight modal that shows a quotation/contract detail page inside an iframe
 * (navbar hidden via embed=1). Driven by the 'viewCommercialItem' event that the
 * datatables emit when $emitView is on.
 */
class Viewer extends Component
{
    public $viewUrl = null;
    public $viewTitle = '';

    protected $listeners = ['viewCommercialItem'];

    public function viewCommercialItem($kind, $id)
    {
        if (! in_array($kind, ['quotation', 'contract'], true)) {
            return;
        }

        $item = $kind === 'quotation' ? Quotation::find($id) : Contract::find($id);
        if (! $item) {
            return;
        }

        $url = route('commercial.edit.show', ['key' => $kind, 'id' => $id]) . '?embed=1';
        if ($item->model === 'PROJECT' && $item->model_id) {
            $url .= '&source=project&id=' . $item->model_id;
        }

        $this->viewUrl = $url;
        $this->viewTitle = $item->title ?: ucfirst($kind);
    }

    public function close()
    {
        $this->viewUrl = null;
        $this->viewTitle = '';
    }

    public function render()
    {
        return view('livewire.commercial.viewer');
    }
}
