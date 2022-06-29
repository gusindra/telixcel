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
        if(auth()->user()->super && auth()->user()->super->first() && auth()->user()->super->first()->role == 'superadmin'){
            return Request::query()->with('agent', 'client');
        }
        return Request::query()->where('requests.user_id', auth()->user()->currentTeam->user_id)->with('agent', 'client');
    }

    public function columns()
    {
        return [
            Column::name('user_id')->label('User ID')->filterable(),
            Column::name('created_at')->label('Creation Date')->filterable(),
    		NumberColumn::name('id')->label('ID')->sortBy('id'),
    		Column::callback(['agent.name', 'from'], function ($agent, $from) {
                if($from == 'bot'){
                    return 'BOT';
                }
                if($from == 'api'){
                    return 'API';
                }
                return $agent;
            })->label('Agent'),
    		Column::name('client.name')->label('Client'),
    		Column::name('reply')->label('Message'),
    		Column::name('type')->label('Type'),
    	];
    }
}
