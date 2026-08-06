<?php

namespace App\Services\Agent;

use App\Models\Billing;
use App\Models\BlastMessage;
use App\Models\Contract;
use App\Models\Order;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Task;
use App\Models\Ticket;

/**
 * Whitelist of models the AI agent is allowed to operate on.
 *
 * Each entry is the single source of truth for: the Eloquent class, which
 * columns may be read/written, status hints for the LLM, and the RBAC
 * permission name (matches Permission->model used by checkPermisissions()).
 * Anything not listed here is rejected by the ToolExecutor.
 */
class ModelRegistry
{
    public static function map(): array
    {
        return [
            'order' => [
                'class' => Order::class,
                'permission' => 'ORDER',
                'label' => 'Order',
                'readable' => ['id', 'no', 'name', 'type', 'status', 'total', 'customer_id', 'user_id', 'source', 'date', 'created_at', 'deleted_at'],
                'writable' => ['name', 'type', 'status', 'total', 'date'],
                'statuses' => ['draft', 'unpaid', 'paid', 'process', 'refund', 'done'],
                'types'    => ['selling', 'saas', 'referral'],
            ],
            'project' => [
                'class' => Project::class,
                'permission' => 'PROJECT',
                'label' => 'Project',
                'readable' => ['id', 'name', 'type', 'status', 'customer_name', 'team_id', 'created_at', 'deleted_at'],
                'writable' => ['name', 'type', 'status', 'customer_name'],
                'statuses' => ['draft', 'submit', 'approved', 'done', 'decline'],
                'types'    => ['selling', 'saas', 'referral'],
            ],
            'task' => [
                'class' => Task::class,
                'permission' => 'PROJECT',
                'label' => 'Task',
                'readable' => ['id', 'project_id', 'title', 'type', 'status', 'priority', 'target_date', 'owner_id', 'created_at', 'updated_at', 'deleted_at'],
                'writable' => ['title', 'type', 'status', 'priority', 'target_date'],
                'statuses'   => ['progress', 'pending', 'complete'],
                'types'      => ['finance', 'admin', 'operasional'],
                'priorities' => ['low', 'medium', 'high'],
                'notes' => 'project_id links to project.id; use "in" op with a list of project_ids for cross-model queries',
            ],
            'contract' => [
                'class' => Contract::class,
                'permission' => 'CONTRACT',
                'label' => 'Contract',
                'readable' => ['id', 'title', 'status', 'model', 'model_id', 'expired_at', 'actived_at', 'client_id', 'user_id', 'created_at', 'deleted_at'],
                'writable' => ['title', 'status', 'expired_at', 'actived_at'],
                'statuses' => ['draft', 'new', 'submit', 'approved', 'done', 'decline'],
                'notes' => 'model_id = project.id when model="PROJECT"; expired_at is a datetime column; use < or <= for expiry queries',
            ],
            'ticket' => [
                'class' => Ticket::class,
                'permission' => 'TICKET',
                'label' => 'Ticket',
                'readable' => ['id', 'status', 'priority', 'reasons', 'solution', 'request_id', 'handled_by', 'forward_to', 'created_at', 'deleted_at'],
                'writable' => ['status', 'reasons', 'solution'],
                'statuses'   => ['open', 'in_progress', 'resolved', 'closed'],
                'priorities' => ['low', 'medium', 'high'],
            ],
            'blast-message' => [
                'class' => BlastMessage::class,
                'permission' => 'CAMPAIGN',
                'label' => 'Blast Message',
                'readable' => ['id', 'msg_id', 'type', 'status', 'msisdn', 'title', 'price', 'currency', 'created_at', 'deleted_at'],
                'writable' => ['status'],
                'statuses' => ['DELIVERED', 'SENT', 'UNDELIVERED', 'PROCESSED', 'ACCEPTED'],
            ],
            'quotation' => [
                'class' => Quotation::class,
                'permission' => 'QUOTATION',
                'label' => 'Quotation',
                'readable' => ['id', 'quote_no', 'title', 'type', 'status', 'price', 'discount', 'client_id', 'date', 'valid_day', 'created_at', 'deleted_at'],
                'writable' => ['title', 'type', 'status', 'price', 'discount', 'date', 'valid_day'],
                'statuses' => ['draft', 'submit', 'approved', 'done', 'decline'],
                'types'    => ['selling', 'saas', 'referral'],
            ],
            'invoice' => [
                'class' => Billing::class,
                'permission' => 'INVOICE',
                'label' => 'Invoice',
                'readable' => ['id', 'code', 'order_id', 'period', 'status', 'amount', 'currency', 'description', 'invoice_date', 'created_at', 'deleted_at'],
                'writable' => ['status', 'amount', 'description', 'period', 'invoice_date'],
                'statuses' => ['draft', 'unpaid', 'paid', 'overdue', 'cancelled'],
            ],
        ];
    }

    public static function resolve(string $key): ?array
    {
        return self::map()[strtolower(trim($key))] ?? null;
    }

    public static function keys(): array
    {
        return array_keys(self::map());
    }
}
