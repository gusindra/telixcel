<?php

namespace App\Http\Livewire\Table;

use App\Models\Team;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class TeamTable extends LivewireDatatable
{
    public $model = Team::class;

    public function builder()
    {
        return Team::query()->join("team_user", "team_user.user_id", "=", "teams.user_id")->groupBy('teams.user_id');
    }

    public function columns()
    {
        return [
    		Column::name('user.id')->filterable()->label('ID'),
    		Column::name('user.name')->filterable()->label('Name'),
    		Column::name('user.email')->filterable()->label('Email'),
    		DateColumn::name('created_at')->label('Register Date')->format('d F Y H:i:s'),
    		Column::name('team_user.team_id')->label('Team'),
    		Column::name('team_user.status')->label('Status'),
    		DateColumn::name('updated_at')->label('Last Online')->format('d F Y H:i:s'),
            NumberColumn::name('user.id')->label('Detail')->sortBy('user.id')->callback(['user.id', 'user.uuid'], function ($id, $uuid) {
                $href = $uuid
                    ? route('user.show', $uuid).'?month='.date('m').'&year='.date('Y')
                    : route('user.show', $id).'?month='.date('m').'&year='.date('Y');

                return view('datatables::link', [
                    'href' => $href,
                    'slot' => 'View'
                ]);
            }),
    	];
    }
}
