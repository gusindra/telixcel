<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            if (! Schema::hasColumn('billings', 'direction')) {
                $table->string('direction')->default('out')->after('status'); // out = bill client, in = vendor bill
            }
            if (! Schema::hasColumn('billings', 'vendor_name')) {
                $table->string('vendor_name')->nullable()->after('direction');
            }
            if (! Schema::hasColumn('billings', 'invoice_date')) {
                $table->date('invoice_date')->nullable()->after('vendor_name');
            }
            if (! Schema::hasColumn('billings', 'attachment')) {
                $table->string('attachment')->nullable()->after('invoice_date'); // uploaded vendor invoice file
            }
        });
    }

    public function down(): void
    {
        Schema::table('billings', function (Blueprint $table) {
            foreach (['direction', 'vendor_name', 'invoice_date', 'attachment'] as $col) {
                if (Schema::hasColumn('billings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
