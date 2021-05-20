<?php

namespace App\Http\Livewire;

use App\Models\Request;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class RequestsTable extends LivewireDatatable
{
    public $model = Request::class;

    public function builder()
    {
        return Request::query()->with('agent', 'client');
    }

    public function columns()
    {
        return [
    		NumberColumn::name('id')->label('ID')->sortBy('id'),
    		Column::callback(['agent.name', 'from'], function ($agent, $from) {
                if($from == 'bot'){
                    return 'BOT';
                }
                return $agent;
            })->label('Agent'),
    		Column::name('client.name')->label('Client'),
    		Column::name('reply')->label('Message'),
    		Column::name('type')->label('Type'),
    		DateColumn::name('created_at')->label('Creation Date')
    	];
    }
}
