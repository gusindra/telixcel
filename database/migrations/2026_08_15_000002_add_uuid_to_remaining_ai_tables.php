<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up()
    {
        foreach (['ai_models', 'ai_requests', 'ai_usage'] as $table) {
            $this->addUuid($table);
        }
    }

    public function down()
    {
        foreach (['ai_models', 'ai_requests', 'ai_usage'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'uuid')) {
                continue;
            }
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropUnique(['uuid']);
                $blueprint->dropColumn('uuid');
            });
        }
    }

    private function addUuid(string $table): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        if (! Schema::hasColumn($table, 'uuid')) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->uuid('uuid')->nullable()->unique()->after('id');
            });
        }

        $rows = DB::table($table)->where(function ($query) {
            $query->whereNull('uuid')->orWhere('uuid', '');
        })->get(['id']);

        foreach ($rows as $row) {
            DB::table($table)->where('id', $row->id)->update([
                'uuid' => (string) Str::uuid(),
            ]);
        }
    }
};
