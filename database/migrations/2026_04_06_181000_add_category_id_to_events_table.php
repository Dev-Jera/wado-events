<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });

        if (Schema::hasColumn('events', 'category')) {
            $categories = DB::table('events')
                ->select('category')
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category');

            foreach ($categories as $name) {
                $categoryId = DB::table('categories')->insertGetId([
                    'name' => $name,
                    'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('events')
                    ->where('category', $name)
                    ->update(['category_id' => $categoryId]);
            }

            Schema::table('events', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('category')->nullable()->after('slug');
        });

        DB::table('events')
            ->leftJoin('categories', 'events.category_id', '=', 'categories.id')
            ->update(['events.category' => DB::raw('categories.name')]);

        Schema::table('events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
