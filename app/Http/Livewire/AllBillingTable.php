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
    		Column::name('amount')->label('Amount'),
    		Column::name('status')->label('Status'),
    		DateColumn::name('created_at')->label('Creation Date')
    	];
    }
}
