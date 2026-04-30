<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Tracks physical presence — completely separate from the payment/validity `status` column.
            // 'outside' = not currently inside the venue (default)
            // 'inside'  = currently inside the venue
            $table->string('gate_status', 10)->default('outside')->after('used_at');
            $table->unsignedSmallInteger('reentry_count')->default(0)->after('gate_status');
            $table->timestamp('last_entry_at')->nullable()->after('reentry_count');
            $table->timestamp('last_exit_at')->nullable()->after('last_entry_at');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['gate_status', 'reentry_count', 'last_entry_at', 'last_exit_at']);
        });
    }
};
