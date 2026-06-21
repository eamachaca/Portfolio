<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('hero_tag')->nullable()->after('resume');

            foreach ([
                'hero_title',
                'hero_copy',
                'hero_note',
                'about_heading',
                'about_body',
                'strengths_heading',
                'strengths_intro',
                'experience_heading',
                'experience_intro',
                'education_heading',
                'portfolio_heading',
                'portfolio_intro',
                'skills_heading',
                'skills_intro',
                'workstyle_heading',
                'workstyle_intro',
                'testimonials_heading',
                'faq_heading',
                'blog_heading',
                'contact_heading',
                'contact_intro',
            ] as $column) {
                $table->json($column)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'hero_tag',
                'hero_title',
                'hero_copy',
                'hero_note',
                'about_heading',
                'about_body',
                'strengths_heading',
                'strengths_intro',
                'experience_heading',
                'experience_intro',
                'education_heading',
                'portfolio_heading',
                'portfolio_intro',
                'skills_heading',
                'skills_intro',
                'workstyle_heading',
                'workstyle_intro',
                'testimonials_heading',
                'faq_heading',
                'blog_heading',
                'contact_heading',
                'contact_intro',
            ]);
        });
    }
};
