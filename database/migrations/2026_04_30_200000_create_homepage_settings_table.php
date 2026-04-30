<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('featured_count')->default(4);
            $table->unsignedTinyInteger('trending_count')->default(4);
            $table->unsignedTinyInteger('upcoming_count')->default(8);
            $table->unsignedTinyInteger('trending_days')->default(14);
            $table->boolean('show_featured')->default(true);
            $table->boolean('show_trending')->default(true);
            $table->boolean('show_upcoming')->default(true);
            $table->string('empty_heading')->default('No upcoming events right now');
            $table->string('empty_sub')->default('We\'re working on bringing new events. Check back soon.');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_settings');
    }
};
