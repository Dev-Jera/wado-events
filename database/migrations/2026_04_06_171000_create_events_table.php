<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('category');
            $table->string('venue');
            $table->string('city');
            $table->string('country');
            $table->text('description');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->decimal('ticket_price', 12, 2);
            $table->unsignedInteger('capacity');
            $table->unsignedInteger('tickets_available');
            $table->string('status')->default('draft');
            $table->string('image_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
