<?php

namespace App\Http\Livewire\Table;

use App\Models\Commision;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class Commission extends LivewireDatatable
{
    public $model = Commision::class;

    public function builder()
    {
        return Commision::query()->where('model', 'order')->orderBy('created_at', 'desc');
    }

    public function columns()
    {
        return [
    		// Column::name('id')->label('ID'),
    		// Column::name('model')->label('Model'),
    		Column::name('data.name')->callback('model, model_id, product.name, project.name, order.name', function ($m, $mi, $p, $v, $o) {
                if($m=='product'){
                    return view('datatables::link', [
                        'href' => "/commercial/item/" . $mi,
                        'slot' => $p
                    ]);
                }elseif($m=='project'){
                    return view('datatables::link', [
                        'href' => "/project/" . $mi,
                        'slot' => $v
                    ]);
                }
                return view('datatables::link', [
                    'href' => "/order/" . $mi,
                    'slot' => $o
                ]);
            })->label('Data')->filterable(),
    		Column::name('agent.name')->callback('client_id, agent.name, agent.user.id', function ($c, $slot, $id) {
                return view('datatables::link', [
                            'href' => "/user/" . $id,
                            'slot' => $slot
                        ]);
            })->label('Agent')->filterable(),
    		Column::name('total')->label('Total'),
    		DateColumn::name('created_at')->label('Created_at')->filterable(),
    		Column::callback(['status'], function ($y) {
                return view('label.label', ['type' => $y]);
            })->label('Status')->filterable(['DRAFT', 'SUBMITED', 'APPROVED', 'RELEASED']),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id', function ($value) {
                return view('datatables::link', [
                    'href' => "/commission/" . $value,
                    'slot' => 'View'
                ]);
            }),

    	];
    }
}
