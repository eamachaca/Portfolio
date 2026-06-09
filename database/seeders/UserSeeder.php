<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'eduardo'],
            [
                'name' => 'Eduardo Machaca',
                'full_name' => 'Eduardo Andrés Machaca Peña',
                'email' => 'eamachaca@icloud.com',
                'password' => Hash::make('password'),
                'headline' => 'Backend Developer',
                'bio' => 'Software Developer with 6+ years of experience developing web applications '
                    . 'using PHP (Laravel), C#, RoR and Services (SOAP & REST), with a focus on '
                    . 'process improvement and automation. Technology enthusiast and learning '
                    . 'fanatic — there should never be excuses to learn something new. Based in '
                    . 'Santa Cruz, Bolivia (GMT-4).',
                'avatar' => 'avatars/eduardo-machaca.jpg',
                'social_links' => [
                    'github' => 'https://github.com/eamachaca',
                    'linkedin' => 'https://www.linkedin.com/in/eamachaca',
                    'workana' => 'https://www.workana.com/freelancer/2dabb583384666d5ff6a1ecc0208bd69',
                ],
                'email_verified_at' => now(),
            ],
        );
    }
}
