<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('error_logs', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index(); // webhook, payment, ticket, etc
            $table->string('severity')->index(); // error, warning, info
            $table->text('message');
            $table->json('context')->nullable(); // additional data
            $table->string('user_id')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('event_id')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['type', 'created_at']);
            $table->index(['severity', 'created_at']);
            $table->index(['resolved_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
