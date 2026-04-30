<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->foreignId('promo_code_id')->nullable()->after('ticket_category_id')->constrained('promo_codes')->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0)->after('unit_price');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\PromoCode::class);
            $table->dropColumn(['promo_code_id', 'discount_amount']);
        });
    }
};
