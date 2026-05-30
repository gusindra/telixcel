<?php

namespace App\Services\Agent;

/**
 * OpenAI-compatible tool definitions passed to Ollama's /api/chat `tools` param.
 * Kept deliberately small (4 tools) for reliable tool-calling.
 */
class ToolSchemas
{
    public static function all(): array
    {
        $models = ModelRegistry::keys();

        return [
            self::tool(
                'query_records',
                'Read / list records of a whitelisted model with optional filters. Executes immediately.',
                [
                    'model' => ['type' => 'string', 'enum' => $models],
                    'filters' => [
                        'type' => 'array',
                        'description' => 'List of {field, op, value}. op must be one of =, !=, like, in.',
                        'items' => self::filterItem(),
                    ],
                    'limit' => ['type' => 'integer', 'description' => 'Max rows to return (1-50, default 20).'],
                ],
                ['model']
            ),

            self::tool(
                'create_record',
                'Create one new record of a whitelisted model. Executes immediately.',
                [
                    'model' => ['type' => 'string', 'enum' => $models],
                    'values' => ['type' => 'object', 'description' => 'column => value pairs (writable columns only).'],
                ],
                ['model', 'values']
            ),

            self::tool(
                'update_record',
                'Propose an UPDATE. Does NOT write — returns a pending change for the user to approve.',
                [
                    'model' => ['type' => 'string', 'enum' => $models],
                    'filters' => ['type' => 'array', 'description' => 'Which records to update.', 'items' => self::filterItem()],
                    'values' => ['type' => 'object', 'description' => 'column => new value pairs (writable columns only).'],
                ],
                ['model', 'filters', 'values']
            ),

            self::tool(
                'delete_record',
                'Propose a DELETE. Does NOT delete — returns a pending change for the user to approve.',
                [
                    'model' => ['type' => 'string', 'enum' => $models],
                    'filters' => ['type' => 'array', 'description' => 'Which records to delete.', 'items' => self::filterItem()],
                ],
                ['model', 'filters']
            ),
        ];
    }

    private static function filterItem(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'field' => ['type' => 'string'],
                'op' => ['type' => 'string', 'enum' => ['=', '!=', 'like', 'in']],
                'value' => ['description' => 'string|number, or array of values when op is "in".'],
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
