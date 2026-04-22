<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Speeds up event dashboard queries (list all tickets for an event by status)
            $table->index(['event_id', 'status'], 'tickets_event_status_idx');

            // Speeds up gate scan audit log queries filtering by status alone
            $table->index('status', 'tickets_status_idx');

            // Speeds up scan log join: "latest scan for each ticket in event X"
            $table->index(['event_id', 'used_at'], 'tickets_event_used_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('tickets_event_status_idx');
            $table->dropIndex('tickets_status_idx');
            $table->dropIndex('tickets_event_used_at_idx');
        });
    }
};
