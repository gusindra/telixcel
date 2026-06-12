<?php

namespace App\Services\Agent;

/**
 * Builds the {SCHEMA_CONTEXT} block injected into the system prompt so the
 * LLM knows which models, columns and status values it may work with.
 */
class SchemaContext
{
    public static function build(): string
    {
        $lines = ['AVAILABLE DATA MODELS (you may only operate on these):'];

        foreach (ModelRegistry::map() as $key => $reg) {
            $cols = implode(', ', $reg['readable']);
            $statuses = isset($reg['statuses'])
                ? ' | status values: ' . implode('/', $reg['statuses'])
                : '';
            $types = isset($reg['types'])
                ? ' | type values: ' . implode('/', $reg['types'])
                : '';
            $priorities = isset($reg['priorities'])
                ? ' | priority values: ' . implode('/', $reg['priorities'])
                : '';
            $notes = isset($reg['notes']) ? ' | note: ' . $reg['notes'] : '';
            $lines[] = "- \"{$key}\" ({$reg['label']}): columns: {$cols}{$statuses}{$types}{$priorities}{$notes}";
        }

        return implode("\n", $lines);
    }
}
