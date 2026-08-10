<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Removed: public agent API (POST /api/agent) + per-user AgentUserToken.
 * Tools now run only inside Laravel (ToolExecutor); Hermes is LLM-only.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('agent_user_tokens');
    }

    public function down(): void
    {
        // Intentionally empty — API path retired; recreate only if reintroducing remote tools.
    }
};
