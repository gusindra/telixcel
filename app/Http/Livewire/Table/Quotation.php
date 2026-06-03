<?php

namespace App\Http\Livewire\Table;


use App\Models\Quotation as ModelsQuotation;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class Quotation extends LivewireDatatable
{
    public $model = ModelsQuotation::class;
    public $export_name = 'DATA_QUOTATION';

    /** Optional: scope the table to a single project. */
    public $project_id;

    /** Optional: scope to a single client (used inside the per-client modal). */
    public $client_id;

    /** When true the "View" detail opens the in-modal iframe (emit) instead of the full page. */
    public $emitView = false;

    public function builder()
    {
        $query = ModelsQuotation::query()->orderBy('quotations.updated_at', 'desc');

        if ($this->project_id) {
            $query->where('quotations.model', 'PROJECT')->where('quotations.model_id', $this->project_id);
        }

        if ($this->client_id) {
            $query->where('quotations.client_id', $this->client_id);
        }

        return $query;
    }

    public function columns()
    {
        return [
    		Column::name('title')->label('Title'),
    		Column::name('model')->callback('model, project.name, company.name, client.name', function ($m, $pn, $com, $cn) {
                if($m=='PROJECT'){
                    return $m.' : '.$pn;
                }elseif($m=='COMPANY'){
                    return $m.' : '.$com;
                }elseif($m=='CLIENT'){
                    return $m.' : '.$cn;
                }
                return $m;
            })->label('Source')->filterable()->exportCallback(function ($value) {
                return (string) $value;
            }),
            Column::callback('client_id', function ($cid) {
                $c = $cid ? \App\Models\Client::find($cid) : null;
                return $c ? $c->name : '-';
            })->label('Client'),
    		DateColumn::name('date')->label('Date')->filterable(),
    		NumberColumn::name('valid_day')->label('Duration (Day)')->filterable(),
    		Column::callback(['status'], function ($status) {
                return view('label.label', ['type' => $status]);
            })->label('Status')->filterable(['DRAFT', 'APPROVED', 'SUBMIT']),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id', function ($value) {
                if ($this->emitView) {
                    return '<button type="button" onclick="Livewire.emit(\'viewCommercialItem\', \'quotation\', ' . (int) $value . ')" '
                        . 'class="text-xs font-medium text-blue-600 hover:text-blue-800">View</button>';
                }

                return view('datatables::link', [
                    'href' => "/commercial/quotation/" . $value,
                    'slot' => 'View'
                ]);
            }),
            Column::callback(['id'], function ($id) {
                return view('tables.delete-action', [
                    'type' => 'quotation',
                    'id' => $id,
                    'label' => 'quotation',
                ]);
            })->label('Delete'),

    	];
    }
}
