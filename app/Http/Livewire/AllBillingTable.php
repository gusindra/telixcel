<?php

namespace App\Http\Livewire;

use App\Models\Billing;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class AllBillingTable extends LivewireDatatable
{
    public $model = Billing::class;

    public function builder()
    {
        return Billing::query();
    }

    public function columns()
    {
        return [
    		NumberColumn::name('code')->label('Transaction ID')->sortBy('code'),
    		Column::name('description')->label('Description'),
    		NumberColumn::name('amount')->callback('amount', function ($value) {
                if($value){
                    return 'Rp'.number_format($value);
                }
                return 0;
            })->label('Amount'),
    		Column::name('status')->label('Status'),
    		DateColumn::name('created_at')->label('Creation Date'),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id, order_id', function ($value, $order) {
                if($order){
                    return view('datatables::link', [
                        'href' => "commercial/". $order."/invoice/print",
                        'slot' => 'Invoice'
                    ]);
                }
                return '';
            }),
    	];
    }
}
