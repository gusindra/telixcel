<?php

namespace App\Http\Livewire\Table;

use App\Models\Contract as ModelsContract;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class Contract extends LivewireDatatable
{
    public $model = ModelsContract::class;
    public $export_name = 'DATA_CONTRACT';

    /** Optional: scope the table to a single project. */
    public $project_id;

    /** Optional: scope to a single client (used inside the per-client modal). */
    public $client_id;

    /** When true the "View" detail opens the in-modal iframe (emit) instead of the full page. */
    public $emitView = false;

    public function builder()
    {
        $query = ModelsContract::query()->orderBy('contracts.updated_at', 'desc');

        if ($this->project_id) {
            $query->where('contracts.model', 'PROJECT')->where('contracts.model_id', $this->project_id);
        }

        if ($this->client_id) {
            $query->where('contracts.client_id', $this->client_id);
        }

        return $query;
    }

    public function columns()
    {
        return [
    		Column::name('title')->label('Title'),
            Column::callback('client_id', function ($cid) {
                $c = $cid ? \App\Models\Client::find($cid) : null;
                return $c ? $c->name : '-';
            })->label('Client'),
    		DateColumn::name('created_at')->label('Created_at')->filterable(),
    		DateColumn::name('expired_at')->label('Expired_at')->filterable(),
    		Column::callback(['status'], function ($s) {
                return view('label.label', ['type' => $s]);
            })->label('Status')->filterable(['DRAFT', 'APPROVED', 'SUBMIT']),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id', function ($value) {
                if ($this->emitView) {
                    return '<button type="button" onclick="Livewire.emit(\'viewCommercialItem\', \'contract\', ' . (int) $value . ')" '
                        . 'class="text-xs font-medium text-blue-600 hover:text-blue-800">View</button>';
                }

                return view('datatables::link', [
                    'href' => "/commercial/contract/" . $value,
                    'slot' => 'View'
                ]);
            }),
            Column::callback(['id'], function ($id) {
                return view('tables.delete-action', [
                    'type' => 'contract',
                    'id' => $id,
                    'label' => 'contract',
                ]);
            })->label('Delete'),

    	];
    }
}
