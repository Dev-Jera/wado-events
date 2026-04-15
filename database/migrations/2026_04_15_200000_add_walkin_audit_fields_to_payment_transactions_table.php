<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table): void {
            $table->string('sales_channel', 20)->nullable()->after('payment_provider');
            $table->foreignId('collected_by_user_id')->nullable()->after('sales_channel')->constrained('users')->nullOnDelete();
            $table->timestamp('collected_at')->nullable()->after('collected_by_user_id');
            $table->string('collector_reference', 120)->nullable()->after('collected_at');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('collected_by_user_id');
            $table->dropColumn([
                'sales_channel',
                'collected_at',
                'collector_reference',
            ]);
        });
    }
};
