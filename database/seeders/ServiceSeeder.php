<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::query()->where('username', 'eduardo')->first();

        if (! $owner) {
            return;
        }

        $services = [
            [
                'title' => 'Backend Development',
                'icon' => 'ti-server',
                'description' => 'Production-grade Laravel APIs and web apps, with tests, queues, '
                    . 'background jobs and an admin panel that the client can actually use.',
            ],
            [
                'title' => 'System Integrations',
                'icon' => 'ti-package',
                'description' => 'SAP Business One, e-invoicing, third-party REST/SOAP services, '
                    . 'scrapper estates with proxy rotation. Bridging the moving parts so the business runs.',
            ],
            [
                'title' => 'Internal Tools & BackOffice',
                'icon' => 'ti-panel',
                'description' => 'Filament-based admin panels for ops teams, with role-based access, '
                    . 'multi-tenancy and runtime branding.',
            ],
            [
                'title' => 'Process Automation',
                'icon' => 'ti-loop',
                'description' => 'Daily crons, recurring jobs, event-driven side effects. '
                    . 'Quietly running in the background so the team stops doing the same thing twice.',
            ],
        ];

        foreach ($services as $i => $s) {
            Service::updateOrCreate(
                ['owner_id' => $owner->id, 'title' => $s['title']],
                ['description' => $s['description'], 'icon' => $s['icon'], 'sort_order' => $i + 1],
            );
        }
    }
}
