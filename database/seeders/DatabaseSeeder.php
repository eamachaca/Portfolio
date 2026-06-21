<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            NetworkSeeder::class,
            SocialLinkSeeder::class,
            StudySeeder::class,
            ExperienceSeeder::class,
            SkillSeeder::class,
            ServiceSeeder::class,
            FaqSeeder::class,
            ProjectSeeder::class,
            TestimonialSeeder::class,
            StrengthSeeder::class,
            WorkStyleItemSeeder::class,
        ]);
    }
}
