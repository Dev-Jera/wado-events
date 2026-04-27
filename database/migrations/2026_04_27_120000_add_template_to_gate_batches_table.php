<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gate_batches', function (Blueprint $table) {
            $table->string('template', 20)->default('classic')->after('ticket_size');
        });
    }

    public function down(): void
    {
        Schema::table('gate_batches', function (Blueprint $table) {
            $table->dropColumn('template');
        });
    }
};
