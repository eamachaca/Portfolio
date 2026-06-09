<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('company');
        });

        // Backfill existing rows.
        \App\Models\Experience::query()->whereNull('slug')->get()->each(function ($e) {
            $e->slug = Str::slug($e->company);
            $e->saveQuietly();
        });

        Schema::table('experiences', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('experiences', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
