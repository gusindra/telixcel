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
        foreach ($this->tables() as $table) {
            if (! Schema::hasTable($table)) {
                continue;
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
    }

    public function down()
    {
        foreach ($this->tables() as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'uuid')) {
                Schema::table($table, function (Blueprint $blueprint) {
                    $blueprint->dropUnique(['uuid']);
                    $blueprint->dropColumn('uuid');
                });
            }
        }
    }

    private function tables(): array
    {
        return [
            'orders',
            'commisions',
            'quotations',
            'contracts',
            'commerce_items',
            'roles',
            'companies',
            'billings',
        ];
    }
};
