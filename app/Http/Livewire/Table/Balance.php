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
        // if(auth()->user()->super && auth()->user()->super->first() && auth()->user()->super->first()->role == 'superadmin'){
        //     return SaldoUser::query();
        // }
        return SaldoUser::query()->where('user_id', $this->user);
    }

    public function columns()
    {
        return [
    		DateColumn::name('created_at')->label('DATE')->format('d/m/Y H:i')->filterable(),
    		Column::callback(['mutation'], function ($y) {
                return view('label.type', ['type' => $y]);
            })->label('MUTATION (DEBIT/CREDIT)')->filterable(['debit', 'credit'])->exportCallback(function ($value) {
                return (string) $value;
            }),
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
