<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->string('fulfilment_status', 32)
                ->default('not_required')
                ->after('service_package_notes')
                ->index();
        });

        DB::table('events')
            ->whereIn('service_package', ['batch_tickets', 'premium_wristbands'])
            ->update(['fulfilment_status' => 'pending']);
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dropIndex(['fulfilment_status']);
            $table->dropColumn('fulfilment_status');
        });
    }
};
