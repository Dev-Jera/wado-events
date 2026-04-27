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
        Schema::create('gate_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->string('label');               // e.g. "General Entry", "VIP"
            $table->decimal('price', 12, 2)->default(0);
            $table->unsignedInteger('quantity');
            $table->enum('ticket_size', ['small', 'standard', 'large'])->default('small');
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'active', 'closed', 'voided'])->default('draft');
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();

            $table->index(['event_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gate_batches');
    }
};
