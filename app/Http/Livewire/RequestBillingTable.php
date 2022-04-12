<?php

namespace App\Http\Livewire;

use App\Models\Request;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class RequestBillingTable extends LivewireDatatable
{
    public $model = Request::class;
    public $userId;
    public $month;
    public $year;

    public function builder()
    {
        return Request::query()->where('user_id', $this->userId)->whereMonth('created_at', $this->month)->whereYear('created_at', $this->year);
    }

    public function columns()
    {
        return [
    		Column::name('client_id')->label('Client ID'),
    		Column::name('reply')->label('Message'),
    		Column::callback(['sent_at'], function ($type) {
                if($type!=null)
                    return 'In';
                return 'Out';
            })->label('Type'),
    		DateColumn::name('created_at')->label('Creation Date')
    	];
    }
}
