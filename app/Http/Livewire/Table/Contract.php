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

    public function columns()
    {
        return [
    		Column::name('title')->label('Title'),
    		Column::name('status')->label('Status')->filterable(['DRAFT', 'APPROVED', 'SUBMIT']),
    		Column::name('model')->label('Source'),
    		DateColumn::name('created_at')->label('Created_at')->filterable(),
    		DateColumn::name('expired_at')->label('Expired_at')->filterable(),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id', function ($value) {
                return view('datatables::link', [
                    'href' => "/commercial/contract/" . $value,
                    'slot' => 'View'
                ]);
            }),

    	];
    }
}
