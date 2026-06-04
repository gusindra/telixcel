<?php

namespace App\Http\Livewire\Invoice;

use App\Models\Billing;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Upload an incoming (vendor) invoice — direction = 'in'.
 * Outgoing invoices (direction = 'out') are generated from orders elsewhere.
 */
class IncomingAdd extends Component
{
    use WithFileUploads;

    public $modalActionVisible = false;

    public $code;
    public $vendor_name;
    public $amount;
    public $description;
    public $invoice_date;
    public $file;

    public function rules()
    {
        return [
            'code' => 'required',
            'vendor_name' => 'required',
            'amount' => 'required|numeric',
            'invoice_date' => 'required|date',
            'description' => 'nullable',
            'file' => 'nullable|file|max:10240', // 10MB
        ];
    }

    public function create()
    {
        $this->validate();

        $path = null;
        if ($this->file) {
            $path = $this->file->store('invoices', 'public');
        }

        Billing::create([
            'uuid'         => (string) Str::uuid(),
            'order_id'     => null,
            'code'         => $this->code,
            'vendor_name'  => $this->vendor_name,
            'amount'       => $this->amount,
            'description'  => $this->description,
            'invoice_date' => $this->invoice_date,
            'attachment'   => $path,
            'direction'    => 'in',
            'status'       => 'unpaid',
            'user_id'      => auth()->id(),
        ]);

        $this->modalActionVisible = false;
        $this->resetForm();
        $this->emit('refreshLivewireDatatable');
    }

    public function resetForm()
    {
        $this->code = null;
        $this->vendor_name = null;
        $this->amount = null;
        $this->description = null;
        $this->invoice_date = null;
        $this->file = null;
    }

    public function actionShowModal()
    {
        $this->modalActionVisible = true;
    }

    public function render()
    {
        return view('livewire.invoice.incoming-add');
    }
}
