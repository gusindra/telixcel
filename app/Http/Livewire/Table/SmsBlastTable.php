<?php

namespace App\Http\Livewire\Table;

use App\Models\BlastMessage;
use Mediconesystems\LivewireDatatables\Action;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Exports\DatatableExport;
use Mediconesystems\LivewireDatatables\LabelColumn;

class SmsBlastTable extends LivewireDatatable
{
    public $model = Client::class;
    public $hideable = 'select';
    public $userId;
    public $month;
    public $year;
    public $export_name = 'SMS_REQUEST';
    public $exportStyles;
    public $exportWidths;

    public function builder()
    {
        if(auth()->user()->super && auth()->user()->super->first() && auth()->user()->super->first()->role == 'superadmin'){
            return BlastMessage::query()->orderBy('created_at', 'desc')->leftJoin('Users as user', 'user_id', 'user.id');
        }
        return BlastMessage::query()->where('blast_messages.user_id', auth()->user()->currentTeam->user_id)->orderBy('created_at', 'desc');
    }

    private function clientTbl(){
        return [
            Column::callback(['status'], function ($y) {
                return view('label.type', ['type' => $y]);
            })->width('100')->label('Status')->filterable(['DELIVERED', 'UNDELIVERED', 'ACCEPTED', 'PROCESSING', 'PROCESSED'])->exportCallback(function ($value) {
                return (string) $value;
            }),
            Column::callback(['client_id'], function ($client_id) {
                return $client_id;
            })->filterable()->label('Client')->hide()->truncate(12)->exportCallback(function ($value) {
                return (string) $value;
            }),
    		Column::name('msisdn')->label('Phone No')->filterable(),
    		Column::name('message_content')->label('Message Content')->truncate(50)->filterable(),
    		DateColumn::name('created_at')->label('Creation Date')->sortBy('created_at', 'desc')->filterable()->alignRight(),
    		DateColumn::raw('blast_messages.created_at AS created_at2')->label('Time')->format('H:i'),
    		Column::name('status')->label('Status')->hide(),
    	];
    }

    private function adminTbl(){
        return [
            Column::checkbox(),
    		Column::callback(['status'], function ($y) {
                return view('label.type', ['type' => $y]);
            })->label('Status')->filterable(['DELIVERED', 'UNDELIVERED', 'ACCEPTED', 'PROCESSING', 'PROCESSED'])->exportCallback(function ($value) {
                return (string) $value;
            }),
    		Column::name('user_id')->callback(['user_id', 'user.name'], function ($value, $u) {
                return view('datatables::link', [
                    'href' => "/user/" . $value,
                    'slot' => $u
                ]);
            })->label('User')->filterable()->exportCallback(function ($value) {
                return (string) $value;
            }),
    		Column::name('created_at')->label('Creation Date')->sortBy('created_at', 'desc')->filterable(),
    		Column::name('price')->label('Price'),
    		Column::name('msg_id')->label('Sending ID'),
    		Column::name('client_id')->hide()->label('Client')->filterable(),
    		Column::name('message_content')->label('Message Content')->filterable(),
    		Column::name('msisdn')->label('Phone No')->filterable(),
    		Column::name('status')->label('Status')->hide(),
    	];
    }

    public function columns()
    {
        if(auth()->user()->super && auth()->user()->super->first() && auth()->user()->super->first()->role == 'superadmin'){
            return $this->adminTbl();
        }
        return $this->clientTbl();
    }

    public function rowClasses($row, $loop)
    {
        if($loop->even){
            return 'divide-x divide-gray-100 text-sm text-gray-900 bg-gray-50 dark:bg-slate-700 text-xs ';
        }
        return 'divide-x divide-gray-100 text-sm text-gray-900 bg-gray-100 dark:bg-slate-800 text-xs ';
    }

    public function cellClasses($row, $column)
    {
        $extra = '';
        if($row->{'status'}==='invalid servid'){
            $extra = 'w-full';
        }
        return 'px-2 text-xs '.$extra;
    }

    // public function buildActions()
    // {
    //     return [

    //         Action::value('edit')->label('Edit Selected')->group('Default Options')->callback(function ($mode, $items) {
    //             // $items contains an array with the primary keys of the selected items
    //         }),

    //         Action::value('update')->label('Update Selected')->group('Default Options')->callback(function ($mode, $items) {
    //             // $items contains an array with the primary keys of the selected items
    //         }),

    //         Action::groupBy('Export Options', function () {
    //             return [
    //                 Action::value('csv')->label('Export CSV')->export('SalesOrders.csv'),
    //                 Action::value('html')->label('Export HTML')->export('SalesOrders.html'),
    //                 Action::value('xlsx')->label('Export XLSX')->export('SalesOrders.xlsx')->styles($this->exportStyles)->widths($this->exportWidths)
    //             ];
    //         }),
    //     ];
    // }
}
