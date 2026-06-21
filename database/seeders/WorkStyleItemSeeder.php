<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WorkStyleItem;
use Illuminate\Database\Seeder;

class WorkStyleItemSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::query()->where('username', 'eduardo')->first();
        if (! $owner) {
            return;
        }

        $items = [
            [
                'en' => 'Practical solutions over fancy buzzwords.',
                'es' => 'Soluciones prácticas antes que buzzwords.',
            ],
            [
                'en' => 'Readable code and direct technical communication.',
                'es' => 'Código legible y comunicación técnica directa.',
            ],
            [
                'en' => 'Comfortable inside existing systems, logs, queues, and edge cases.',
                'es' => 'Cómodo dentro de sistemas existentes, logs, colas y edge cases.',
            ],
            [
                'en' => 'Curious with new tools, but only when they solve the problem.',
                'es' => 'Curioso con herramientas nuevas, pero solo cuando resuelven el problema.',
            ],
        ];

        foreach ($items as $i => $text) {
            WorkStyleItem::updateOrCreate(
                ['owner_id' => $owner->id, 'sort_order' => $i],
                ['owner_id' => $owner->id, 'text' => $text, 'sort_order' => $i],
            );
        }
    }
}
