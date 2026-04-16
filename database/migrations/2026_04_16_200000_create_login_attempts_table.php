<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255);
            $table->string('ip_address', 45);        // supports IPv4 and IPv6
            $table->boolean('succeeded')->default(false);
            $table->string('user_agent', 500)->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // set on success
            $table->string('failure_reason', 120)->nullable(); // set on failure
            $table->timestamp('attempted_at')->useCurrent();

            // Indexes for admin queries and IP analysis
            $table->index('ip_address');
            $table->index('email');
            $table->index('attempted_at');
            $table->index(['succeeded', 'attempted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
