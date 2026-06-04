<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A quotation/contract belongs to ONE client (many-to-one), so a direct
 * client_id foreign key is the normalized form. quotations.client_id already
 * exists; this adds the same to contracts for consistency.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('contracts') && ! Schema::hasColumn('contracts', 'client_id')) {
            Schema::table('contracts', function (Blueprint $t) {
                $t->unsignedBigInteger('client_id')->nullable()->index()->after('model_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('contracts') && Schema::hasColumn('contracts', 'client_id')) {
            Schema::table('contracts', function (Blueprint $t) {
                $t->dropColumn('client_id');
            });
        }
    }
};
