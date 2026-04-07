<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ticket_category_id')->constrained()->cascadeOnDelete();
            $table->string('ticket_code')->unique();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('payment_provider', 20)->default('free');
            $table->string('status', 40)->default('confirmed');
            $table->dateTime('purchased_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
