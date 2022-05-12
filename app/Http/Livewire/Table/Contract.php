<?php

namespace App\Http\Livewire\Table;

use App\Models\Contract as ModelsContract;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;

class Contract extends LivewireDatatable
{
    public $model = ModelsContract::class;

    public function columns()
    {
        return [
    		Column::name('title')->label('Title'),
    		Column::name('status')->label('Status'),
    		Column::name('model')->label('Source'),
    		Column::name('created_at')->label('Created_at'),
    		Column::name('expired_at')->label('Expired_at'),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id', function ($value) {
                return view('datatables::link', [
                    'href' => "/commercial/contract/" . $value,
                    'slot' => 'View'
                ]);
            }),

    	];
    }
}
