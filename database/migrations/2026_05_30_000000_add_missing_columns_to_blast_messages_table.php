<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The original add_blast_messages_table migration only created
 * msg_id, user_id, client_id, sender_id, type, status, message_content.
 * But the BlastMessage model's $fillable and app code (e.g. Helper::masterSaldo,
 * which runs `sum(price) where otp = 1 and balance != 0`) expect more columns
 * that were added directly in the production DB and never captured as a migration.
 *
 * This reconciles a freshly-migrated schema with what the code expects.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blast_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('blast_messages', 'balance')) {
                $table->decimal('balance', 20, 2)->default(0)->after('message_content');
            }
            if (! Schema::hasColumn('blast_messages', 'msisdn')) {
                $table->string('msisdn')->nullable()->after('balance');
            }
            if (! Schema::hasColumn('blast_messages', 'title')) {
                $table->string('title')->nullable()->after('msisdn');
            }
            if (! Schema::hasColumn('blast_messages', 'price')) {
                $table->decimal('price', 20, 2)->default(0)->after('title');
            }
            if (! Schema::hasColumn('blast_messages', 'code')) {
                $table->string('code')->nullable()->after('price');
            }
            if (! Schema::hasColumn('blast_messages', 'currency')) {
                $table->string('currency', 10)->nullable()->after('code');
            }
            if (! Schema::hasColumn('blast_messages', 'otp')) {
                $table->boolean('otp')->default(false)->after('currency');
            }
        });
    }

    public function down(): void
    {
        Schema::table('blast_messages', function (Blueprint $table) {
            foreach (['balance', 'msisdn', 'title', 'price', 'code', 'currency', 'otp'] as $col) {
                if (Schema::hasColumn('blast_messages', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
