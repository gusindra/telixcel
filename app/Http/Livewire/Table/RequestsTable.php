<?php

namespace App\Http\Livewire\Table;

use App\Models\Request;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class RequestsTable extends LivewireDatatable
{
    public $model = Request::class;
    public $hideable = 'select';
    public $export_name = 'CHAT_REQUEST';

    public function builder()
    {
        if(auth()->user()->super && auth()->user()->super->first() && auth()->user()->super->first()->role == 'superadmin'){
            return Request::query()->with('agent', 'client')->orderBy('created_at', 'desc');
        }
        return Request::query()->where('requests.user_id', auth()->user()->currentTeam->user_id)->with('agent', 'client')->orderBy('created_at', 'desc');
    }

    public function columns()
    {
        return [
            Column::name('user_id')->label('User ID')->filterable(),
            Column::name('created_at')->label('Creation Date')->filterable(),
    		NumberColumn::name('id')->label('ID')->sortBy('id'),
    		Column::callback(['from', 'agent.name'], function ($from, $agent) {
                if($from == 'bot'){
                    return 'BOT';
                }
                if($from == 'api'){
                    return 'API';
                }
            })->label('Agent ID'),
            Column::callback(['agent.name'], function ($agent) {
                if($agent){
                    return $agent;
                }
                return '-';
            })->label('Agent Name')->hide()->exportCallback(function ($value) {
                return (string) $value ?? '-';
            }),
    		Column::callback(['client_id'], function ($id) {
                return $id;
            })->label('Client ID')->exportCallback(function ($value) {
                return (string) $value;
            }),
            Column::callback(['client.name'], function ($client) {
                if($client){
                    return $client;
                }
                return '-';
            })->label('Client Name')->hide()->exportCallback(function ($value) {
                return (string) $value ?? '-';
            }),
    		Column::name('reply')->label('Message'),
    		Column::callback(['type'], function ($y) {
                return view('label.type', ['type' => $y]);
            })->label('Type'),
    	];
    }
}
