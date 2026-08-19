<?php

namespace App\Http\Livewire\Table;

use App\Models\AiRequest;
use Mediconesystems\LivewireDatatables\Column;
use Mediconesystems\LivewireDatatables\DateColumn;
use Mediconesystems\LivewireDatatables\Http\Livewire\LivewireDatatable;

class AiRequests extends LivewireDatatable
{
    public $model = AiRequest::class;
    public $applicationId;

    public function builder()
    {
        $query = AiRequest::query()
            ->leftJoin('ai_applications', 'ai_applications.id', '=', 'ai_requests.ai_application_id')
            ->leftJoin('ai_usage', 'ai_usage.ai_request_id', '=', 'ai_requests.id')
            ->select('ai_requests.*')
            ->orderByDesc('ai_requests.id');

        if ($this->applicationId) {
            $query->where('ai_requests.ai_application_id', $this->applicationId);
        }

        return $query;
    }

    public function columns()
    {
        $columns = [
            Column::name('ai_requests.request_id')->label('Request ID')->searchable()->filterable(),
        ];

        if (! $this->applicationId) {
            $columns[] = Column::name('ai_applications.name')->label('Application')->filterable()->searchable();
        }

        return array_merge($columns, [
            Column::name('ai_requests.end_user_name')->label('User name')->hide()->searchable()->filterable(),
            Column::callback(['ai_requests.end_user_name', 'ai_requests.end_user_id', 'ai_requests.feature'], function ($name, $id, $feature) {
                $line = $name ?: '-';
                if ($id) {
                    $line .= ' ('.$id.')';
                }
                if ($feature) {
                    $line .= ' / '.$feature;
                }

                return e($line);
            })->label('End user')->searchable(),
            Column::name('ai_requests.end_user_id')->label('User ID')->hide()->searchable()->filterable(),
            Column::name('ai_requests.model')->label('Model')->filterable()->searchable(),
            Column::name('ai_requests.status')->label('Status')->filterable(['success', 'error', 'pending']),
            Column::name('ai_requests.latency_ms')->label('Latency'),
            Column::name('ai_usage.input_tokens')->label('In'),
            Column::name('ai_usage.output_tokens')->label('Out'),
            Column::name('ai_usage.total_tokens')->label('Tokens'),
            Column::name('ai_usage.cost')->label('Cost'),
            DateColumn::name('ai_requests.created_at')->label('Created')->filterable(),
        ]);
    }
}
