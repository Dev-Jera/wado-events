<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gate_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('gate_batches')->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('ticket_code')->unique();
            $table->text('qr_payload');            // signed JSON stored for QR rendering
            $table->enum('status', ['unprinted', 'sold', 'used', 'void'])->default('unprinted');
            $table->timestamp('sold_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->foreignId('used_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['batch_id', 'status']);
            $table->index(['event_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_tickets');
    }
};
