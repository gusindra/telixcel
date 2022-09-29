<?php

namespace App\Http\Livewire\Table;

use App\Models\BlastMessage;
use App\Models\OperatorPhoneNumber;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\BooleanColumn;

class SmsBlastTable extends LivewireDatatable
{
    public $model = Client::class;
    public $hideable = 'select';
    public $userId;
    public $month;
    public $year;
    public $export_name = 'SMS_REQUEST';


    public function builder()
    {
        if((auth()->user()->super && auth()->user()->super->first() && auth()->user()->super->first()->role == 'superadmin') || (auth()->user()->activeRole && str_contains(auth()->user()->activeRole->role->name, "Admin"))){
            return BlastMessage::query()->orderBy('created_at', 'desc');
        }
        return BlastMessage::query()->where('blast_messages.user_id', auth()->user()->currentTeam->user_id)->orderBy('created_at', 'desc');
    }

    private function clientTbl(){
        return [
            Column::callback(['status'], function ($y) {
                return view('label.type', ['type' => $y]);
            })->label('Status')->filterable(['DELIVERED', 'UNDELIVERED', 'ACCEPTED', 'PROCESSING', 'PROCESSED'])->exportCallback(function ($value) {
                return (string) $value;
            }),
            Column::callback(['client_id'], function ($client_id) {
                    return $client_id;
            })->filterable()->label('Client')->truncate(12)->filterable()->label('Client')->hide()->exportCallback(function ($value) {
                return (string) $value;
            }),
            Column::name('sender_id')->label('Sender Name')->filterable(),
    		Column::name('msisdn')->label('Phone No')->filterable(),
    		Column::name('message_content')->label('Message Content')->truncate(50)->filterable(),
    		DateColumn::name('created_at')->label('Creation Date')->sortBy('created_at', 'desc')->format('d-m-Y H:i:s')->filterable()->alignRight(),
    		DateColumn::name('updated_at')->label('Updated Status')->format('H:i:s'),
    		Column::name('status')->label('Status')->hide(),
    		Column::callback(['msisdn'], function ($nohp) {
                if(strlen($nohp)>6){
                    return OperatorPhoneNumber::where('code', substr($nohp, 0, 5))->first()->operator;
                }
                return "-";
            })->label('OP')->hide(),
    		BooleanColumn::name('otp')->hide()->label('OTP')
    	];
    }

    private function adminTbl(){
        return [
            Column::callback(['id', 'msisdn', 'status', 'msg_id', 'code'], function ($id, $m, $s, $mid, $c) {
                return view('tables.sms-action', ['id' => $id, 'msisdn' => $m, 'status' => $s, 'mid' => $mid, 'code' => $c ]);
            })->label('Action'),
    		Column::callback(['status'], function ($y) {
                return view('label.type', ['type' => $y]);
            })->label('Status')->filterable(['DELIVERED', 'UNDELIVERED', 'ACCEPTED', 'PROCESSING', 'PROCESSED'])->label('Status')->exportCallback(function ($value) {
                return (string) $value;
            }),
    		Column::name('user_id')->callback(['user_id'], function ($value) {
                return view('datatables::link', [
                    'href' => "/user/" . $value,
                    'slot' => $value
                ]);
            })->label('User')->filterable()->exportCallback(function ($value) {
                return (string) $value;
            }),
            DateColumn::name('updated_at')->hide()->label('Update Date')->filterable()->format('d-m-Y H:i:s'),
    		DateColumn::name('created_at')->label('Creation Date')->sortBy('created_at', 'desc')->filterable()->format('d-m-Y H:i:s'),
    		Column::name('price')->label('Price'),
    		Column::name('msg_id')->label('Sending ID'),
    		Column::name('client_id')->hide()->label('Client')->filterable(),
    		Column::name('message_content')->label('Message Content')->filterable(),
    		Column::name('msisdn')->label('Phone No')->filterable(),
    		Column::name('sender_id')->label('Sender Name')->filterable(),
    		Column::name('status')->label('Status')->hide(),
    		Column::callback(['msisdn'], function ($nohp) {
                if(strlen($nohp)>6){
                    return OperatorPhoneNumber::where('code', substr($nohp, 0, 5))->first()->operator;
                }
                return "-";
            })->label('OP')->hide(),
    		BooleanColumn::name('otp')->hide()->label('OTP')
    	];
    }

    public function columns()
    {
        if((auth()->user()->super && auth()->user()->super->first() && auth()->user()->super->first()->role == 'superadmin') || (auth()->user()->activeRole && str_contains(auth()->user()->activeRole->role->name, "Admin"))){
            return $this->adminTbl();
        }
        return $this->clientTbl();
    }

    public function cellClasses($row, $column)
    {
        //return $row->{'status'};
        $extra = '';
        if(str_contains(strtolower($row->{'status'}), 'invalid')){
            $extra = 'w-full';
        }
        return 'px-2 text-xs '.$extra;
    }
}
