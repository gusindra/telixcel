<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Laravel's $table->float() maps to MySQL DOUBLE(8,2) — maximum 999,999.99.
 * Any line-item / commerce-item price above ~Rp 1 juta is silently clamped to
 * 999,999.99 on insert (non-strict mode), corrupting the amount. Widen the money
 * columns to DECIMAL(20,2) so they can hold real Rupiah amounts.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $columns = [
            'order_products' => ['price'],
            'commerce_items' => ['price', 'fs_price', 'unit_price', 'general_discount'],
            'orders'         => ['total'],
            'quotations'     => ['price', 'discount'],
            'commisions'     => ['total'],
        ];

        foreach ($columns as $table => $cols) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            foreach ($cols as $col) {
                if (Schema::hasColumn($table, $col)) {
                    DB::statement("ALTER TABLE `{$table}` MODIFY `{$col}` DECIMAL(20,2) NOT NULL DEFAULT 0");
                }
            }
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        $columns = [
            'order_products' => ['price'],
            'commerce_items' => ['price', 'fs_price', 'unit_price', 'general_discount'],
            'orders'         => ['total'],
            'quotations'     => ['price', 'discount'],
            'commisions'     => ['total'],
        ];

        foreach ($columns as $table => $cols) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            foreach ($cols as $col) {
                if (Schema::hasColumn($table, $col)) {
                    DB::statement("ALTER TABLE `{$table}` MODIFY `{$col}` DOUBLE(8,2) NOT NULL DEFAULT 0");
                }
            }
        }
    }
};
