<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Temporary hardcoded content so the front renders before the backOffice
 * (Filament) and the database are populated.
 *
 * TODO: remove once content is managed from the admin. The controllers show
 * the DB-backed query commented out, ready to switch over.
 */
class SampleContent
{
    public static function user(): object
    {
        return (object) [
            'name' => 'Eduardo Machaca',
            'headline' => "I'm a Full-Stack Developer",
            'bio' => 'Desarrollador full-stack enfocado en Laravel y aplicaciones web. '
                . 'Esta es una bio de muestra; se reemplaza desde el backOffice.',
            'email' => 'eamachaca@icloud.com',
            'avatar' => null, // null => usa la imagen de ReFrame por defecto
            'social_links' => [
                'github' => 'https://github.com/eamachaca',
                'linkedin' => 'https://www.linkedin.com/',
            ],
        ];
    }

    public static function projects(): Collection
    {
        return collect([
            (object) [
                'title' => 'Project One',
                'excerpt' => 'A short tagline for the first project.',
                'description' => 'Sample description. Replace this from the backOffice. '
                    . 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'cover_image' => 'reframe/assets/images/work/item-1/preview.jpg',
                'gallery' => [],
                'tech_stack' => ['Laravel', 'Filament', 'MySQL'],
                'url' => null,
                'repo_url' => 'https://github.com/eamachaca/Portfolio',
                'featured' => true,
            ],
            (object) [
                'title' => 'Project Two',
                'excerpt' => 'Another highlighted project.',
                'description' => 'Sample description. Replace this from the backOffice.',
                'cover_image' => 'reframe/assets/images/work/item-2/preview.jpg',
                'gallery' => [],
                'tech_stack' => ['PHP', 'Tailwind', 'Alpine.js'],
                'url' => null,
                'repo_url' => null,
                'featured' => true,
            ],
            (object) [
                'title' => 'Project Three',
                'excerpt' => 'A third sample project.',
                'description' => 'Sample description. Replace this from the backOffice.',
                'cover_image' => 'reframe/assets/images/work/item-3/preview.jpg',
                'gallery' => [],
                'tech_stack' => ['Vue', 'Inertia'],
                'url' => null,
                'repo_url' => null,
                'featured' => false,
            ],
        ]);
    }

    public static function studies(): Collection
    {
        return collect([
            (object) [
                'institution' => 'Universidad',
                'title' => 'Ingeniería de Sistemas',
                'field' => null,
                'description' => 'Estudio de muestra; se reemplaza desde el backOffice.',
                'start_date' => Carbon::create(2019, 1, 1),
                'end_date' => Carbon::create(2024, 1, 1),
                'in_progress' => false,
            ],
        ]);
    }
}
