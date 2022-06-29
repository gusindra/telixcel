<?php

namespace App\Http\Livewire;

use App\Models\User;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class UsersTable extends LivewireDatatable
{
    public $model = User::class;

    public function builder()
    {
        return User::query()->where('user_id', auth()->user()->currentTeam->user_id);
    }

    public function columns()
    {
        return [
    		NumberColumn::name('uuid')->label('ID'),
    		Column::name('name')->label('Name')->sortBy('name'),
    		Column::name('phone')->label('Phone Number'),
    		DateColumn::name('created_at')->label('Creation Date')
    	];
    }
}
