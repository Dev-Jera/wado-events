<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->string('service_package', 32)
                ->default('online_ticketing')
                ->after('category_id')
                ->index();

            $table->text('service_package_notes')
                ->nullable()
                ->after('service_package');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dropIndex(['service_package']);
            $table->dropColumn(['service_package', 'service_package_notes']);
        });
    }
};
