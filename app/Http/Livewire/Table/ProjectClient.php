<?php

namespace App\Http\Livewire\Table;

use App\Models\Client;
use App\Models\Project;
use Illuminate\Support\Facades\DB;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class ProjectClient extends LivewireDatatable
{
    public $model = Client::class;
    public $export_name = 'DATA_PROJECT_CLIENT';

    /** Required: which project's clients to list. */
    public $project_id;

    /**
     * Keep the package's own datatable listeners (refresh/sort/filter) and add ours.
     * Declaring a fresh array here would shadow the parent and break auto-refresh + sorting.
     */
    public $listeners = [
        'refreshLivewireDatatable',
        'complexQuery',
        'resetTable',
        'sortTable',
        'client_attached' => 'refreshLivewireDatatable',
    ];

    public function builder()
    {
        $clientIds = DB::table('project_client')
            ->where('project_id', $this->project_id)
            ->pluck('client_id')
            ->all();

        return Client::query()->whereIn('clients.id', $clientIds ?: [0])->orderBy('clients.name');
    }


    public function columns()
    {
        return [
            Column::name('title')->label('Title'),
            Column::name('sender')->label('PIC / Sender')->searchable(),
            Column::name('name')->label('Name')->searchable(),
            Column::name('phone')->label('Phone')->searchable(),
            Column::name('email')->label('Email')->searchable(),
            Column::callback(['id'], function ($id) {
                $id = (int) $id;

                return '<button type="button" onclick="Livewire.emit(\'confirmDetachClient\', ' . $id . ')" '
                    . 'class="text-xs font-medium text-red-600 hover:text-white hover:bg-red-600 border border-red-300 rounded-md px-3 py-1.5 transition">Remove</button>';
            })->label('Action'),
        ];
    }
}
