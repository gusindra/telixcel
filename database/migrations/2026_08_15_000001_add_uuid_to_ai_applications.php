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
        if (! Schema::hasTable('ai_applications')) {
            return;
        }

        if (! Schema::hasColumn('ai_applications', 'uuid')) {
            Schema::table('ai_applications', function (Blueprint $table) {
                $table->uuid('uuid')->nullable()->unique()->after('id');
            });
        }

        $rows = DB::table('ai_applications')->whereNull('uuid')->orWhere('uuid', '')->get(['id']);
        foreach ($rows as $row) {
            DB::table('ai_applications')->where('id', $row->id)->update([
                'uuid' => (string) Str::uuid(),
            ]);
        }
    }

    public function down()
    {
        if (Schema::hasTable('ai_applications') && Schema::hasColumn('ai_applications', 'uuid')) {
            Schema::table('ai_applications', function (Blueprint $table) {
                $table->dropUnique(['uuid']);
                $table->dropColumn('uuid');
            });
        }
    }
};
