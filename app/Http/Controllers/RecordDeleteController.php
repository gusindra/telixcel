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
use App\Models\LogChange;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;

class RecordDeleteController extends Controller
{
    /** Records may only be deleted while in one of these (un-approved) states. */
    private const DELETABLE_STATUSES = ['draft', 'new', 'revise', 'disabled', 'cancel'];

    /** Only these types follow the approval flow (draft deletable, approved locked). */
    private const APPROVAL_TYPES = ['project', 'quotation', 'contract', 'order', 'invoice', 'commission'];

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

        // Guard: for approval-flow records, once approved (or otherwise locked) they cannot be deleted.
        if (in_array($type, self::APPROVAL_TYPES, true) && $this->isLocked($record)) {
            return redirect()->back()->with('error', 'Cannot delete: record is already approved.');
        }

        // Audit before deleting.
        $this->logChange($type, $record);

        $record->delete();

        return redirect()->back()->with('status', 'Data deleted.');
    }

    /**
     * A record is locked when it has a status that is NOT in the deletable set.
     * Records without a status column are always deletable.
     */
    private function isLocked($record): bool
    {
        $status = $record->status ?? null;
        if ($status === null || $status === '') {
            return false;
        }

        return ! in_array(strtolower($status), self::DELETABLE_STATUSES, true);
    }

    private function logChange(string $type, $record): void
    {
        try {
            LogChange::create([
                'model'    => $type,
                'model_id' => $record->getKey(),
                'before'   => $record->toJson(),
                'remark'   => 'Deleted by ' . (auth()->user()->name ?? 'system'),
            ]);
        } catch (\Throwable $e) {
            // Auditing must never block the delete action.
        }
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
            'task' => Task::class,
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
