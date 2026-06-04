<?php

namespace App\Http\Livewire\Table;

use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

/**
 * Client list for the Commercial tab. Each row has Quotation/Contract buttons
 * that emit 'openClientList' (handled by the ClientCommercial component).
 */
class ProjectClientCommercial extends LivewireDatatable
{
    public $model = Client::class;
    public $export_name = 'DATA_PROJECT_CLIENT';

    public $project_id;

    /** Which action button to show: '' = both, 'quotation', or 'contract'. */
    public $only = '';

    public $listeners = [
        'refreshLivewireDatatable',
        'complexQuery',
        'resetTable',
        'sortTable',
    ];

    public function builder()
    {
        $ids = DB::table('project_client')->where('project_id', $this->project_id)->pluck('client_id')->all();

        return Client::query()->whereIn('clients.id', $ids ?: [0])->orderBy('clients.name');
    }

    public function columns()
    {
        return [
            Column::name('name')->label('Client')->searchable(),
            Column::name('sender')->label('PIC / Sender')->searchable(),
            Column::name('phone')->label('Phone')->searchable(),
            Column::name('email')->label('Email')->searchable(),
            Column::callback(['id'], function ($id) {
                $id = (int) $id;
                $only = $this->only;

                $q = '<button type="button" onclick="Livewire.emit(\'openClientList\', ' . $id . ', \'quotation\')" '
                    . 'class="text-xs font-medium text-blue-600 hover:text-white hover:bg-blue-600 border border-blue-300 rounded-md px-3 py-1.5 transition">View Quotation</button>';
                $c = '<button type="button" onclick="Livewire.emit(\'openClientList\', ' . $id . ', \'contract\')" '
                    . 'class="text-xs font-medium text-green-600 hover:text-white hover:bg-green-600 border border-green-300 rounded-md px-3 py-1.5 transition">View Contract</button>';

                $buttons = $only === 'quotation' ? $q : ($only === 'contract' ? $c : $q . $c);

                return '<div class="flex gap-2">' . $buttons . '</div>';
            })->label($this->only === 'contract' ? 'Contract' : ($this->only === 'quotation' ? 'Quotation' : 'Commercial')),
        ];
    }
}
