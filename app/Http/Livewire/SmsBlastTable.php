<?php

namespace App\Http\Livewire;

use App\Models\BlastMessage;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class SmsBlastTable extends LivewireDatatable
{
    public $model = Client::class;
    public $userId;
    public $month;
    public $year;

    public function builder()
    {
        return BlastMessage::query(); //->where('user_id', $this->userId);
    }

    public function columns()
    {
        return [
    		Column::name('msg_id')->label('Sending ID'),
    		Column::name('client_id')->label('Client'),
    		Column::name('message_content')->label('Message Content'),
    		Column::name('status')->label('Status'),
    		Column::name('msisdn')->label('Phone No'),
    		DateColumn::name('created_at')->label('Creation Date')->sortBy('created_at', 'desc')
    	];
    }
}
