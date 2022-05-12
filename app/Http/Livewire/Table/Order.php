<?php

namespace App\Http\Livewire\Table;

use App\Models\Order as ModelsOrder;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;


class Order extends LivewireDatatable
{
    public $model = ModelsOrder::class;

    public function columns()
    {
        return [
    		Column::name('name')->label('Name'),
    		Column::name('type')->label('Type'),
    		Column::name('entity_party')->label('Party'),
    		Column::name('created_at')->label('Created_at'),
    		Column::name('total')->label('Total'),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id', function ($value) {
                return view('datatables::link', [
                    'href' => "/order/" . $value,
                    'slot' => 'View'
                ]);
            }),

    	];
    }
}
