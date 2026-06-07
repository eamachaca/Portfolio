<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('headline')->nullable()->after('username');
            $table->text('bio')->nullable()->after('headline');
            $table->string('avatar')->nullable()->after('bio');
            $table->json('social_links')->nullable()->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'headline', 'bio', 'avatar', 'social_links']);
        });
    }
};
