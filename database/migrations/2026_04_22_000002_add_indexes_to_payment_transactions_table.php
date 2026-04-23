<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->index('created_at', 'payment_transactions_created_at_idx');
            $table->index(['event_id', 'status'], 'payment_transactions_event_status_idx');
            $table->index(['user_id', 'status'], 'payment_transactions_user_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropIndex('payment_transactions_created_at_idx');
            $table->dropIndex('payment_transactions_event_status_idx');
            $table->dropIndex('payment_transactions_user_status_idx');
        });
    }
};
