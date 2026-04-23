<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->index('status', 'events_status_idx');
            $table->index('starts_at', 'events_starts_at_idx');
            $table->index('is_featured', 'events_is_featured_idx');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropIndex('events_status_idx');
            $table->dropIndex('events_starts_at_idx');
            $table->dropIndex('events_is_featured_idx');
        });
    }
};
