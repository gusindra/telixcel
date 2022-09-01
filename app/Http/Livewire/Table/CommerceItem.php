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
    		Column::name('description')->truncate(150)->label('Description'),
    		Column::callback(['type'], function ($y) {
                return view('label.type', ['type' => $y]);
            })->label('Type')->searchable(),
            Column::callback(['id', 'name', 'source'], function ($id, $name, $s) {
                return view('tables.product-actions', ['id' => $id, 'name' => $name, 'url' =>  "/commercial/item/" . $id, 'source' => $s ]);
            })->label('Action')

    	];
    }
}
