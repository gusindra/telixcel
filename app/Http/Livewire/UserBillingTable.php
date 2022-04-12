<?php

namespace App\Http\Livewire;

use App\Models\Client;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class UserBillingTable extends LivewireDatatable
{
    public $model = Client::class;
    public $userId;
    public $month;
    public $year;

    public function builder()
    {
        return Client::query()->where('user_id', $this->userId)->whereMonth('created_at', '<=', $this->month)->whereYear('created_at', '<=', $this->year);
    }

    public function columns()
    {
        return [
    		Column::name('uuid')->label('ID'),
    		Column::name('name')->label('Name'),
    		Column::name('phone')->label('Phone Number'),
    		DateColumn::name('created_at')->label('Creation Date')
    	];
    }
}
