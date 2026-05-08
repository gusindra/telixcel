<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Commision;
use App\Models\CommerceItem;
use App\Models\Company;
use App\Models\Contract;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Permission;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;

class RecordDeleteController extends Controller
{
    public function destroy(string $type, int $id): RedirectResponse
    {
        $model = $this->modelFor($type);

        if (! $model) {
            abort(404);
        }

        if ($this->isAdminOnly($type) && ! $this->isSuperAdmin()) {
            abort(404);
        }

        $record = $model::findOrFail($id);
        $record->delete();

        return redirect()->back()->with('status', 'Data deleted.');
    }

    private function modelFor(string $type): ?string
    {
        return [
            'project' => Project::class,
            'commerce-item' => CommerceItem::class,
            'quotation' => Quotation::class,
            'contract' => Contract::class,
            'order' => Order::class,
            'invoice' => Billing::class,
            'commission' => Commision::class,
            'company' => Company::class,
            'role' => Role::class,
            'permission' => Permission::class,
            'notification' => Notification::class,
        ][$type] ?? null;
    }

    private function isAdminOnly(string $type): bool
    {
        return in_array($type, [
            'company',
            'role',
            'permission',
            'notification',
        ], true);
    }

    private function isSuperAdmin(): bool
    {
        return auth()->user()->super->first()?->role === 'superadmin';
    }
}
