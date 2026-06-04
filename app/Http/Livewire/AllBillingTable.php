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
        return Billing::query()->orderBy('billings.created_at', 'desc');
    }

    public function columns()
    {
        return [
    		NumberColumn::name('code')->label('Transaction ID')->sortBy('code'),
            Column::callback(['direction'], function ($d) {
                $d = strtolower($d ?? 'out');
                if ($d === 'in') {
                    return '<span class="border border-transparent border-amber-400 bg-amber-50 text-amber-600 text-xs font-bold rounded-md uppercase py-1 px-2">Masuk</span>';
                }
                return '<span class="border border-transparent border-green-400 bg-green-50 text-green-600 text-xs font-bold rounded-md uppercase py-1 px-2">Keluar</span>';
            })->label('Type')->filterable(['in', 'out']),
    		Column::name('description')->label('Description')->filterable(),
            Column::name('vendor_name')->label('Vendor')->callback('vendor_name', function ($v) {
                return $v ?: '-';
            }),
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
            Column::callback(['id'], function ($id) {
                return view('tables.delete-action', [
                    'type' => 'invoice',
                    'id' => $id,
                    'label' => 'invoice',
                ]);
            })->label('Delete'),
    	];
    }
}
