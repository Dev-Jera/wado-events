<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table): void {
            $table->timestamp('refund_requested_at')->nullable()->after('refunded_at');
            $table->string('refund_request_status', 40)->nullable()->after('refund_requested_at');
            $table->text('refund_request_reason')->nullable()->after('refund_request_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table): void {
            $table->dropColumn([
                'refund_requested_at',
                'refund_request_status',
                'refund_request_reason',
            ]);
        });
    }
};
