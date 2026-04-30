<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code', 32)->unique();
            $table->enum('discount_type', ['percentage', 'flat'])->default('percentage');
            $table->decimal('discount_value', 10, 2); // % (0-100) or flat UGX amount
            $table->decimal('max_discount_amount', 10, 2)->nullable(); // cap for percentage codes
            $table->decimal('min_order_amount', 10, 2)->nullable();    // minimum ticket price
            $table->unsignedInteger('max_uses')->nullable();           // null = unlimited
            $table->unsignedInteger('uses')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
