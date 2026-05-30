<?php

namespace App\Services\Agent;

use App\Models\BlastMessage;
use App\Models\Order;
use App\Models\Project;
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
                'readable' => ['id', 'no', 'name', 'type', 'status', 'total', 'customer_id', 'user_id', 'source', 'date', 'created_at'],
                'writable' => ['name', 'type', 'status', 'total', 'date'],
                'statuses' => ['draft', 'unpaid', 'paid'],
            ],
            'project' => [
                'class' => Project::class,
                'permission' => 'PROJECT',
                'label' => 'Project',
                'readable' => ['id', 'name', 'type', 'status', 'customer_name', 'team_id', 'created_at'],
                'writable' => ['name', 'type', 'status', 'customer_name'],
                'statuses' => ['draft', 'active', 'done'],
            ],
            'ticket' => [
                'class' => Ticket::class,
                'permission' => 'TICKET',
                'label' => 'Ticket',
                'readable' => ['id', 'status', 'reasons', 'solution', 'request_id', 'handled_by', 'forward_to', 'created_at'],
                'writable' => ['status', 'reasons', 'solution'],
                'statuses' => ['open', 'handle', 'waiting'],
            ],
            'blast-message' => [
                'class' => BlastMessage::class,
                'permission' => 'CAMPAIGN',
                'label' => 'Blast Message',
                'readable' => ['id', 'msg_id', 'type', 'status', 'msisdn', 'title', 'price', 'currency', 'created_at'],
                'writable' => ['status'],
                'statuses' => ['DELIVERED', 'SENT', 'UNDELIVERED', 'PROCESSED', 'ACCEPTED'],
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
