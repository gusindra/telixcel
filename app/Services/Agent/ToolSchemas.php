<?php

namespace App\Services\Agent;

/**
 * OpenAI-compatible tool definitions for Hermes chat/completions `tools`.
 * Executed only on Laravel via ToolExecutor (never on the VPS).
 */
class ToolSchemas
{
    public static function all(): array
    {
        $models = ModelRegistry::keys();

        return [
            self::tool(
                'query_records',
                'Read / list records of a whitelisted model with optional filters. Executes immediately against the live database. Always returns the "id" field.',
                [
                    'model' => ['type' => 'string', 'enum' => $models],
                    'filters' => [
                        'type' => 'array',
                        'description' => 'List of {field, op, value}. op: =, !=, like, in, >, <, >=, <=.',
                        'items' => self::filterItem(),
                    ],
                    'limit' => ['type' => 'integer', 'description' => 'Max rows (1-50, default 20).'],
                ],
                ['model']
            ),

            self::tool(
                'update_record',
                'Propose an UPDATE. Does NOT write — returns a pending change for the user to approve in the UI. Use "id" for a single record or "filters" for bulk.',
                [
                    'model' => ['type' => 'string', 'enum' => $models],
                    'id' => ['type' => 'integer', 'description' => 'Single record ID from a previous query_records result.'],
                    'filters' => [
                        'type' => 'array',
                        'description' => 'Bulk filter. Ignored when id is provided.',
                        'items' => self::filterItem(),
                    ],
                    'values' => [
                        'type' => 'object',
                        'description' => 'column => new value pairs (writable columns only).',
                    ],
                ],
                ['model', 'values']
            ),

            self::tool(
                'generate_report',
                'Generate monthly task report DATA for display in chat. Does NOT create PDF — use download_report for that.',
                [
                    'type' => [
                        'type' => 'string',
                        'enum' => ['admin', 'user'],
                        'description' => '"admin" (Super Admin only) or "user" (personal).',
                    ],
                    'month' => ['type' => 'integer', 'description' => 'Month (1-12). Default: current.'],
                    'year' => ['type' => 'integer', 'description' => 'Year. Default: current.'],
                ],
                ['type']
            ),

            self::tool(
                'download_report',
                'Generate PDF from a previously generated report (background job). Requires report_id from generate_report.',
                [
                    'report_id' => [
                        'type' => 'integer',
                        'description' => 'The report_id returned by generate_report.',
                    ],
                ],
                ['report_id']
            ),
        ];
    }

    private static function filterItem(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'field' => ['type' => 'string'],
                'op' => [
                    'type' => 'string',
                    'enum' => ['=', '!=', 'like', 'in', '>', '<', '>=', '<='],
                ],
                'value' => ['description' => 'string|number, or array for "in".'],
            ],
            'required' => ['field', 'op', 'value'],
        ];
    }

    private static function tool(string $name, string $description, array $properties, array $required): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $name,
                'description' => $description,
                'parameters' => [
                    'type' => 'object',
                    'properties' => $properties,
                    'required' => $required,
                ],
            ],
        ];
    }
}
