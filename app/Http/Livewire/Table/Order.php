<?php

namespace App\Http\Livewire\Table;

use App\Models\Order as ModelsOrder;
use App\Models\Project;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;


class Order extends LivewireDatatable
{
    public $model = ModelsOrder::class;

    /** Optional: scope the table to a single project's orders. */
    public $project_id;

    public function builder()
    {
        $query = ModelsOrder::query()->orderBy('orders.created_at', 'desc');

        if ($this->project_id) {
            // Orders belong to a project directly (source=PROJECT) OR via its quotations (source=QUOTATION).
            $quotationIds = Project::find($this->project_id)?->quotations->pluck('id')->all() ?? [];

            $query->where(function ($q) use ($quotationIds) {
                $q->where(function ($sub) {
                    $sub->where('orders.source', 'PROJECT')->where('orders.source_id', $this->project_id);
                })->orWhere(function ($sub) use ($quotationIds) {
                    $sub->where('orders.source', 'QUOTATION')->whereIn('orders.source_id', $quotationIds ?: [0]);
                });
            });
        }

        return $query;
    }

    public function columns()
    {
        return [
    		Column::name('no')->label('No'),
    		Column::name('name')->label('Name'),
    		Column::callback('company.name', function ($value) {
                if($value){
                    return $value;
                }
                return '-';
            })->label('Party')->filterable(),
    		DateColumn::name('created_at')->format('d F Y')->label('Created_at')->filterable(),
    		Column::name('total')->callback('total', function ($value) {
                if($value){
                    return 'Rp'.number_format($value);
                }
                return 0;
            })->label('Total'),
            Column::callback(['status'], function ($status) {
                return view('label.label', ['type' => $status]);
            })->label('Status')->filterable(['DRAFT', 'UNPAID', 'PAID', 'CANCEL']),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id', function ($value) {
                return view('datatables::link', [
                    'href' => "/order/" . $value,
                    'slot' => 'View'
                ]);
            }),
            Column::callback(['id'], function ($id) {
                return view('tables.delete-action', [
                    'type' => 'order',
                    'id' => $id,
                    'label' => 'order',
                ]);
            })->label('Delete'),

    	];
    }
}
