<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('reentry_allowed')->default(false)->after('is_free');
            $table->unsignedTinyInteger('reentry_limit')->default(1)->after('reentry_allowed');
            $table->unsignedSmallInteger('reentry_cooldown_minutes')->default(0)->after('reentry_limit');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['reentry_allowed', 'reentry_limit', 'reentry_cooldown_minutes']);
        });
    }
};
