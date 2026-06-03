<?php

namespace App\Http\Livewire\Project;

use App\Models\Client;
use App\Models\Contract;
use App\Models\OrderProduct;
use App\Models\Quotation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Client-driven commercial flow for a project's Commercial tab.
 *
 *  L1  client datatable (Table\ProjectClientCommercial) with Quotation/Contract buttons
 *  L2  click button -> opens list modal of that client's quotations/contracts + Create
 *  L3  Create -> opens form modal (data loaded only when opened)
 *
 * Quotation/Contract stay model=PROJECT (model_id=project) + normalized client_id.
 */
class ClientCommercial extends Component
{
    public $project_id;

    /** When false, only the modal renders (the L1 client datatable is hidden). */
    public $showTable = true;

    public $clientId;
    public $clientName;

    /** '', 'quotation-list', 'contract-list', 'quotation-create', 'contract-create', 'quotation-view', 'contract-view' */
    public $step = '';

    /** Embedded (navbar-less) URL of the quotation/contract being viewed in the iframe. */
    public $viewUrl = null;
    public $viewTitle = '';

    // create-quotation form
    public $q_type;
    public $q_title;
    public $q_date;
    public $q_valid_day;

    // create-contract form
    public $c_title;

    protected $listeners = ['openClientList', 'viewCommercialItem' => 'viewItem'];

    public function mount($id, $showTable = true)
    {
        $this->project_id = $id;
        $this->showTable = $showTable;
    }

    /** Open directly to the client's quotation/contract list (from the row buttons). */
    public function openClientList($clientId, $type)
    {
        $this->clientId = $clientId;
        $this->clientName = optional(Client::find($clientId))->name ?? '';
        $this->step = $type . '-list';
    }

    public function openCreate($type)
    {
        if ($type === 'quotation') {
            $this->q_type = '';
            $this->q_title = '';
            $this->q_date = now()->toDateString();
            $this->q_valid_day = '30';
        } else {
            $this->c_title = '';
        }
        $this->step = $type . '-create';
    }

    public function back($to)
    {
        $this->step = $to;
        $this->viewUrl = null;
    }

    /** Open a quotation/contract detail page inside the modal iframe (navbar hidden via embed=1). */
    public function viewItem($kind, $id)
    {
        if (! in_array($kind, ['quotation', 'contract'], true)) {
            return;
        }
        $this->viewUrl = route('commercial.edit.show', ['key' => $kind, 'id' => $id])
            . '?source=project&id=' . $this->project_id . '&embed=1';
        $this->viewTitle = optional(
            $kind === 'quotation' ? Quotation::find($id) : Contract::find($id)
        )->title ?? ucfirst($kind);
        $this->step = $kind . '-view';
    }

    public function close()
    {
        $this->step = '';
    }

    public function createQuotation()
    {
        $this->validate([
            'q_type' => 'required',
            'q_title' => 'required',
            'q_date' => 'required|date',
            'q_valid_day' => 'required',
        ]);

        $quotation = Quotation::create([
            'type'      => $this->q_type,
            'title'     => $this->q_title,
            'date'      => $this->q_date,
            'valid_day' => $this->q_valid_day,
            'model'     => 'PROJECT',
            'model_id'  => $this->project_id,
            'client_id' => $this->clientId,
            'status'    => 'draft',
            'user_id'   => Auth::id(),
        ]);

        $this->copyItemsFromLatest($quotation);

        $this->step = 'quotation-list';
        $this->emit('refreshLivewireDatatable');
    }

    public function createContract()
    {
        $this->validate(['c_title' => 'required']);

        Contract::create([
            'title'     => $this->c_title,
            'status'    => 'draft',
            'model'     => 'PROJECT',
            'model_id'  => $this->project_id,
            'client_id' => $this->clientId,
            'user_id'   => Auth::id(),
        ]);

        $this->step = 'contract-list';
        $this->emit('refreshLivewireDatatable');
    }

    /** Copy line items from the latest quotation of the SAME client into the new one. */
    private function copyItemsFromLatest(Quotation $new): void
    {
        $latest = Quotation::where('model_id', $this->project_id)
            ->where('client_id', $this->clientId)
            ->where('id', '!=', $new->id)
            ->orderBy('id', 'desc')
            ->first();

        if (! $latest) {
            return;
        }

        foreach (OrderProduct::where('model', 'Quotation')->where('model_id', $latest->id)->get() as $item) {
            OrderProduct::create([
                'name'             => $item->name,
                'model'            => 'Quotation',
                'model_id'         => $new->id,
                'product_id'       => $item->product_id,
                'qty'              => $item->qty,
                'unit'             => $item->unit,
                'price'            => $item->price,
                'total_percentage' => $item->total_percentage,
                'note'             => $item->note,
                'user_id'          => Auth::id(),
            ]);
        }
    }

    private function quotationList()
    {
        if ($this->step !== 'quotation-list' || ! $this->clientId) {
            return collect();
        }

        return Quotation::where('model_id', $this->project_id)
            ->where('client_id', $this->clientId)
            ->orderBy('id', 'desc')->get();
    }

    private function contractList()
    {
        if ($this->step !== 'contract-list' || ! $this->clientId) {
            return collect();
        }

        return Contract::where('model_id', $this->project_id)
            ->where('client_id', $this->clientId)
            ->orderBy('id', 'desc')->get();
    }

    public function render()
    {
        return view('livewire.project.client-commercial', [
            'quotations' => $this->quotationList(),
            'contracts'  => $this->contractList(),
        ]);
    }
}
