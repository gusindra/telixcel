<?php

namespace App\Http\Livewire\Table;

use App\Models\BlastMessage;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class SmsBlastTable extends LivewireDatatable
{
    public $model = Client::class;
    public $hideable = 'select';
    public $userId;
    public $month;
    public $year;

    public function builder()
    {
        if(auth()->user()->super && auth()->user()->super->first() && auth()->user()->super->first()->role == 'superadmin'){
            return BlastMessage::query()->orderBy('created', 'desc');
        }
        return BlastMessage::query()->where('blast_messages.user_id', auth()->user()->currentTeam->user_id)->orderBy('created', 'desc');
    }

    private function clientTbl(){
        return [
            Column::callback(['status'], function ($y) {
                return view('label.type', ['type' => $y]);
            })->label('Status')->filterable(['DELIVERED', 'UNDELIVERED', 'ACCEPTED', 'PROCESSING', 'PROCESSED']),
            Column::callback(['client_id', 'client.name'], function ($client_id, $client_name) {
                if($client_name==''){
                    return $client_id;
                }
                return $client_name;
            })->filterable()->label('Client')->truncate(12)->exportCallback(function ($value) {
                return (string) $value;
            }),
    		Column::name('msisdn')->label('Phone No')->filterable(),
    		Column::name('message_content')->label('Message Content')->truncate(8),
    		DateColumn::name('created_at')->label('Creation Date')->sortBy('created_at', 'desc')->filterable()->alignRight(),
    		DateColumn::raw('blast_messages.created_at AS created_at2')->label('Time')->format('H:i'),
    	];
    }

    private function adminTbl(){
        return [
    		Column::callback(['status'], function ($y) {
                return view('label.type', ['type' => $y]);
            })->label('Status')->filterable(['DELIVERED', 'UNDELIVERED', 'ACCEPTED', 'PROCESSING', 'PROCESSED']),
    		Column::name('user_id')->callback('user_id', function ($value) {
                return view('datatables::link', [
                    'href' => "/user/" . $value,
                    'slot' => $value
                ]);
            })->label('User ID')->filterable(),
    		Column::name('created_at')->label('Creation Date')->sortBy('created_at', 'desc')->filterable(),
    		Column::name('price')->label('Price'),
    		Column::name('msg_id')->label('Sending ID'),
    		Column::name('client_id')->label('Client')->filterable(),
    		Column::name('message_content')->label('Message Content'),
    		Column::name('msisdn')->label('Phone No')->filterable(),
    	];
    }

    public function columns()
    {
        if(auth()->user()->super && auth()->user()->super->first() && auth()->user()->super->first()->role == 'superadmin'){
            return $this->adminTbl();
        }
        return $this->clientTbl();
    }
}
