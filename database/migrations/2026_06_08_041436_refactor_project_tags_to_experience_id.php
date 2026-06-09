<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('experience_id')
                ->nullable()
                ->after('owner_id')
                ->constrained('experiences')
                ->nullOnDelete();
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('tags');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->json('tags')->nullable()->after('apps');
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('experience_id');
        });
    }
};
