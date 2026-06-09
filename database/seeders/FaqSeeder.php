<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::query()->where('username', 'eduardo')->first();

        if (! $owner) {
            return;
        }

        $faqs = [
            [
                'question' => 'Are you available for new projects?',
                'answer' => 'Yes — open to backend / integration work, remote, GMT-4. Drop a message via the contact form below and I will get back within a couple of days.',
            ],
            [
                'question' => 'What stack do you usually work in?',
                'answer' => 'Laravel + PHP for the backend, MySQL/PostgreSQL/SingleStore for storage, AWS for infra, Filament for admin panels. Comfortable in C#, Ruby on Rails, React Native and Flutter when the project needs it.',
            ],
            [
                'question' => 'Do you take on short-term or freelance work?',
                'answer' => 'Yes, with the right scope. I have shipped freelance work through Workana for international clients before.',
            ],
            [
                'question' => 'In which timezone do you work?',
                'answer' => 'I am based in Santa Cruz, Bolivia (GMT-4). Comfortable overlapping with American and European teams.',
            ],
        ];

        foreach ($faqs as $i => $f) {
            Faq::updateOrCreate(
                ['owner_id' => $owner->id, 'question' => $f['question']],
                ['answer' => $f['answer'], 'sort_order' => $i + 1],
            );
        }
    }
}
