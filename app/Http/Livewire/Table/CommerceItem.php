<?php

namespace App\Http\Livewire\Table;

use App\Models\CommerceItem as ModelsCommerceItem;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;

class CommerceItem extends LivewireDatatable
{
    public $model = ModelsCommerceItem::class;

    public function columns()
    {
        return [
    		Column::name('sku')->label('SKU')->searchable(),
    		Column::name('name')->label('Name')->searchable(),
    		Column::name('description')->label('Description'),
    		Column::name('type')->label('Type')->searchable(),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id', function ($value) {
                return view('datatables::link', [
                    'href' => "/commercial/item/" . $value,
                    'slot' => 'View'
                ]);
            }),

    	];
    }
}
