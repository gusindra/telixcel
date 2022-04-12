<?php

namespace App\Http\Livewire;

use App\Models\Notification;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class NotificationTable extends LivewireDatatable
{
    public $model = Notification::class;

    public function builder()
    {
        return Notification::query()->where('user_id', auth()->user()->id)->orderBy('created_at', 'DESC');
    }

    public function columns()
    {
        return [
    		Column::name('type')->label('Name'),
    		Column::name('notification')->label('Description'),
    		DateColumn::name('created_at')->label('Date'),
            Column::callback(['status'], function ($type) {
                return view('template.label', ['type' => $type]);
            }),
            NumberColumn::name('id')->label('Ticket')->sortBy('id')->callback('id', function ($value) {
                return view('datatables::link', [
                    'href' => "/notif-center/" . $value,
                    'slot' => 'View'
                ]);
            }),
    	];
    }
}
