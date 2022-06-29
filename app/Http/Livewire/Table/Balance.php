<?php

namespace App\Http\Livewire\Table;

use App\Models\SaldoUser;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\NumberColumn;
use Mediconesystems\LivewireDatatables\DateColumn;

class Balance extends LivewireDatatable
{
    public $model = SaldoUser::class;
    public $user;

    public function builder()
    {
        return SaldoUser::query()->where('user_id', $this->user);
    }

    public function columns()
    {
        return [
    		DateColumn::name('created_at')->label('DATE')->format('d/m/Y H:i')->filterable(),
    		Column::name('mutation')->label('MUTATION (DEBIT/CREDIT)')->filterable(['debit', 'credit']),
    		Column::name('description')->label('DESCRIPTION')->filterable(),
    		Column::name('currency')->label('Currency')->filterable(),
    		Column::callback(['amount', 'mutation'], function ($amount, $mutation) {
                return view('user.amount', ['amount' => $amount, 'mutation'=> $mutation]);
            })->label('NOMINAL (RP)')->alignRight()->exportCallback(function ($value) {
                return (string) $value;
            }),
    		Column::callback(['balance', 'mutation'], function ($balance, $mutation) {
                return view('user.amount', ['amount' => $balance, 'mutation'=> '']);
            })->label('BALANCE (RP)')->alignRight()->exportCallback(function ($value) {
                return (string) $value;
            }),
    	];
    }
}
