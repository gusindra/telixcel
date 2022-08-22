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
        return Billing::query()->orderBy('created_at', 'desc');
    }

    public function columns()
    {
        return [
    		NumberColumn::name('code')->label('Transaction ID')->sortBy('code'),
    		Column::name('description')->label('Description')->filterable(),
    		NumberColumn::name('amount')->callback('amount', function ($value) {
                if($value){
                    return 'Rp'.number_format($value);
                }
                return 0;
            })->label('Amount'),
    		DateColumn::name('created_at')->label('Creation Date')->filterable(),
    		Column::callback(['status'], function ($y) {
                return view('label.label', ['type' => $y]);
            })->label('Status')->filterable(['PAID', 'UNPAID']),
            NumberColumn::name('id')->label('Detail')->sortBy('id')->callback('id, order_id', function ($value, $order) {
                $link = '';
                if($order){
                    $link = view('datatables::link', [
                        'href' => "invoice-order/". $value,
                        'slot' => 'View'
                    ]);
                }
                return $link;
            }),
    	];
    }
}
