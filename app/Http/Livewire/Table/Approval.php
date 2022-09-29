<?php

namespace App\Http\Livewire\Table;

use App\Models\Billing;
use App\Models\Contract;
use App\Models\FlowProcess;
use App\Models\Order;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Role;
use Mediconesystems\LivewireDatatables\BooleanColumn;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class Approval extends LivewireDatatable
{
    public $model = FlowProcess::class;
    public $user;
    // public $complex = true;

    public function builder()
    {
        if(auth()->user()->super && auth()->user()->super->first() && auth()->user()->super->first()->role == 'superadmin'){
            return FlowProcess::query()->orderBy('created_at', 'asc')->whereNull('user_id')->whereNull('status');
        }else{
            // filter by role
            return FlowProcess::query()->whereNull('user_id')->whereNull('status')->whereIn('role_id', auth()->user()->role->pluck('role_id'))->orderBy('created_at', 'asc');
        }
        // return FlowProcess::query()->where('user_id', $this->user);
    }

    public function columns()
    {
        return [
            Column::index($this),
            DateColumn::name('created_at')->label('Created at')->format('d F Y H:i')->filterable(),
    		Column::callback(['model', 'model_id'], function ($m, $id) {
                $m = strtolower($m);
                if($m=='product'){
                    return view('datatables::link', [
                        'href' => "/commercial/item/" . $id,
                        'slot' => $m
                    ]);
                }elseif($m=='project'){
                    $slot = Project::find($id);
                    return view('datatables::link', [
                        'href' => "/project/" . $id,
                        'slot' => 'Project: '.$slot->name
                    ]);
                }elseif($m=='order'){
                    $slot = Order::find($id);
                    return view('datatables::link', [
                        'href' => "/order/" . $id,
                        'slot' => 'Order: '.$slot->name
                    ]);
                }elseif($m=='quotation'){
                    $slot = Quotation::find($id);
                    return view('datatables::link', [
                        'href' => "/commercial/quotation/" . $id,
                        'slot' => 'Quotation: '.$slot->title
                    ]);
                }elseif($m=='contract'){
                    $slot = Contract::find($id);
                    return view('datatables::link', [
                        'href' => "/commercial/contract/" . $id,
                        'slot' => 'Contract: '.$slot->title
                    ]);
                }elseif($m=='order'){
                    $slot = Order::find($id);
                    return view('datatables::link', [
                        'href' => "/order/" . $id,
                        'slot' => 'Order: '.$slot->name
                    ]);
                }elseif($m=='commission'){
                    return view('datatables::link', [
                        'href' => "/commission/" . $id,
                        'slot' => $m
                    ]);
                }elseif($m=='invoice'){
                    $slot = Billing::find($id);
                    return view('datatables::link', [
                        'href' => "/invoice-order/" . $id,
                        'slot' => 'Invoice: '.$slot->code
                    ]);
                }
            })->label('Data'),
    		Column::name('task')->label('Task')->filterable(),
            Column::callback(['role_id'], function ($id) {
                $role = Role::find($id);
                if($role){
                    return $role->name;
                }
                return '-';
            })->label('Role'),
    		// Column::callback(['status'], function ($status) {
            //     return view('label.label', ['type' => $status]);
            // })->label('Status')->filterable([NULL, 'DECLINE', 'APPROVED', 'SUBMIT']),
    		Column::callback(['user_id'], function ($status) {
                if($status!=null)
                    return '<svg class="h-5 w-5 stroke-current text-green-600 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>';
                return '<center>Waiting for Approval</center>';
            })->label('Process'),

    	];
    }
}
