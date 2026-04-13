<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->string('holder_name')->nullable()->after('ticket_category_id');
            $table->string('payer_name')->nullable()->after('holder_name');
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('holder_name')->nullable()->after('ticket_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn(['holder_name', 'payer_name']);
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn('holder_name');
        });
    }
};
