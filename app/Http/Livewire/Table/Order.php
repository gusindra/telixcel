<?php

namespace App\Http\Livewire\Table;

use App\Models\Order as ModelsOrder;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;


class Order extends LivewireDatatable
{
    public $model = ModelsOrder::class;

    public function builder()
    {
        return ModelsOrder::query()->orderBy('created_at', 'desc');
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

    	];
    }
}
