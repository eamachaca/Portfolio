<?php

namespace Database\Seeders;

use App\Models\Strength;
use App\Models\User;
use Illuminate\Database\Seeder;

class StrengthSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::query()->where('username', 'eduardo')->first();
        if (! $owner) {
            return;
        }

        $items = [
            [
                'label' => ['en' => 'Web Applications', 'es' => 'Aplicaciones Web'],
                'title' => [
                    'en' => 'Laravel apps with clear flows, useful panels, and solid foundations.',
                    'es' => 'Apps en Laravel con flujos claros, paneles útiles y bases sólidas.',
                ],
                'body' => [
                    'en' => 'I build web products around real workflows: dashboards, admin panels, business rules, reports, integrations, and interfaces people can actually use.',
                    'es' => 'Construyo productos web alrededor de flujos reales: dashboards, paneles, reglas de negocio, reportes, integraciones e interfaces que la gente puede realmente usar.',
                ],
                'tech_stack' => ['Laravel', 'PHP', 'Filament', 'Blade', 'Vue'],
            ],
            [
                'label' => ['en' => 'Automation & Data', 'es' => 'Automatización y Datos'],
                'title' => [
                    'en' => 'Scripts, scraping, jobs, and integrations that remove repetitive work.',
                    'es' => 'Scripts, scraping, jobs e integraciones que eliminan el trabajo repetitivo.',
                ],
                'body' => [
                    'en' => 'I work with queues, browser automation, external APIs, data flows, and scraping pipelines when the product needs more than a standard CRUD screen.',
                    'es' => 'Trabajo con colas, automatización de navegador, APIs externas, flujos de datos y pipelines de scraping cuando el producto necesita más que un CRUD estándar.',
                ],
                'tech_stack' => ['Python', 'Selenium', 'Puppeteer', 'Node.js', 'AWS Lambda'],
            ],
            [
                'label' => ['en' => 'Product-minded Delivery', 'es' => 'Entrega con mirada de producto'],
                'title' => [
                    'en' => 'Readable code, clean handoff, and details that make the product feel better.',
                    'es' => 'Código legible, entrega prolija y detalles que mejoran cómo se siente el producto.',
                ],
                'body' => [
                    'en' => 'I care about the whole path: understanding the problem, shaping a usable solution, debugging the rough edges, and communicating clearly with the team.',
                    'es' => 'Me importa el camino completo: entender el problema, dar forma a una solución usable, debuggear los bordes ásperos y comunicarme claro con el equipo.',
                ],
                'tech_stack' => ['SQL', 'Redis', 'Tailwind', 'GitLab', 'Jira'],
            ],
        ];

        foreach ($items as $i => $data) {
            Strength::updateOrCreate(
                ['owner_id' => $owner->id, 'sort_order' => $i],
                $data + ['owner_id' => $owner->id, 'sort_order' => $i],
            );
        }
    }
}
