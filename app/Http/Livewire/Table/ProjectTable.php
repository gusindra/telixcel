<?php

namespace App\Http\Livewire\Table;

use App\Models\FlowSetting;
use App\Models\Project;
use Mediconesystems\LivewireDatatables\BooleanColumn;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;
use Mediconesystems\LivewireDatatables\NumberColumn;

class ProjectTable extends LivewireDatatable
{
    public $model = Project::class;
    public $export_name = 'DATA_PROJECT';

    public function builder()
    {
        $query = Project::query()->orderBy('updated_at', 'desc');

        // Approval gates visibility: managers see all; everyone else only sees
        // APPROVED projects, plus their own drafts/submits, plus submitted ones
        // they are an approver for.
        if ($this->isManager()) {
            return $query;
        }

        $uid = auth()->id();
        $myRoleIds = auth()->user()->role->map(fn ($r) => optional($r->role)->id)->filter()->all();
        $approverRoleIds = FlowSetting::where('model', 'PROJECT')->pluck('role_id')->all();
        $iAmApprover = ! empty(array_intersect($myRoleIds, $approverRoleIds));

        return $query->where(function ($q) use ($uid, $iAmApprover) {
            $q->where('status', 'approved')
              ->orWhere('user_id', $uid);
            if ($iAmApprover) {
                $q->orWhere('status', 'submit');
            }
        });
    }

    private function isManager(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }
        if ($user->super->first()?->role === 'superadmin') {
            return true;
        }
        return $user->activeRole && str_contains($user->activeRole->role->name ?? '', 'Admin');
    }

    public function columns()
    {
        return [
    		Column::name('name')->label('Name')->filterable()->exportCallback(function ($value) {
                return (string) $value ?? '-';
            }),
    		Column::name('id')->label('ID')->filterable(),
    		Column::name('customer_name')->label('Customer')->filterable(),
    		Column::callback(['type'], function ($type) {
                return view('label.type', ['type' => $type]);
            })->label('Type')->filterable(['Selling', 'SAAS', 'Referral']),
            Column::callback(['status'], function ($status) {
                return view('label.label', ['type' => $status]);
            })->label('Status')->filterable(['DRAFT', 'APPROVED', 'SUBMIT']),
            NumberColumn::name('link')->label('Link')->callback('id', function ($value) {
                return view('tables.link', [
                    'href' => "/project/" . $value,
                    'slot' => 'View'
                ]);
            }),
            Column::callback(['id'], function ($id) {
                return view('tables.delete-action', [
                    'type' => 'project',
                    'id' => $id,
                    'label' => 'project',
                ]);
            })->label('Delete'),

    	];
    }
}
