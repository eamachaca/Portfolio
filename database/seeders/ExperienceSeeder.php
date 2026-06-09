<?php

namespace Database\Seeders;

use App\Models\Experience;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::query()->where('username', 'eduardo')->first();

        if (! $owner) {
            return;
        }

        $jobs = [
            [
                'company' => 'Vehicle Acquisition Network',
                'summary' => 'Day-to-day ownership of a large scrapper estate — overseeing failed '
                    . 'jobs and shipping fixes as issues arise on a daily basis.',
                'tech_stack' => [
                    'Laravel', 'Git', 'GitLab', 'Jira', 'Vapor', 'Forge',
                    'SingleStore', 'AWS', 'Redis', 'EC2', 'S3',
                ],
                'sort_order' => 1,
                'levels' => [
                    [
                        'role' => 'Ssr. Backend Engineer',
                        'start_date' => '2022-07-01',
                        'end_date' => null,
                        'in_progress' => true,
                        'description' => null,
                        'highlights' => [
                            'Work with scrappers, using proxies and improving the infrastructure.',
                            'Evolve scrapper methods, steps and the surrounding tooling.',
                        ],
                    ],
                ],
            ],
            [
                'company' => 'Workana',
                'summary' => 'Led projects for many customers, almost always as a single developer '
                    . 'or inside a small team. Agile methodologies for time management.',
                'tech_stack' => [
                    'Laravel', 'PHP', 'Ruby on Rails',
                    'Angular', 'Vue', 'JavaScript', 'C#', 'Flutter', 'Git', 'Jira', 'ClickUp',
                ],
                'sort_order' => 2,
                'levels' => [
                    [
                        'role' => 'Ssr. Backend Engineer',
                        'start_date' => '2021-09-01',
                        'end_date' => '2022-05-01',
                        'in_progress' => false,
                        'description' => null,
                        'highlights' => [
                            'International projects and clients: PKT1, Skymedia, Nubefact, EntertainmentATT, and others.',
                            'Agile methodologies to improve time management across parallel customers.',
                        ],
                    ],
                ],
            ],
            [
                'company' => 'iCorebiz',
                'summary' => 'Led projects for several customers. Measured requirements time '
                    . 'and organized the team\'s work.',
                'tech_stack' => [
                    'Laravel', 'PHP', 'SAP B1 Service Layer', 'DiApi SAP B1',
                    'C#', '.NET Framework', 'Ruby on Rails', 'React Native',
                    'Android Studio', 'JavaScript', 'Java', 'Git', 'Jira', 'Kanban', 'Scrum',
                ],
                'sort_order' => 3,
                'levels' => [
                    [
                        'role' => 'Ssr. Software Engineer',
                        'start_date' => '2021-01-01',
                        'end_date' => '2021-06-01',
                        'in_progress' => false,
                        'description' => null,
                        'highlights' => [
                            'Improved communication between the customer and the development team.',
                            'Learned to communicate with internal customers and suppliers, and got better at analyzing requirements and translating them into functions — large reduction in analysis/planning time, higher delivery quality, fewer requirement errors.',
                            'Improved organization in at least 15% of the projects I led, assigning tasks based on each developer\'s expertise.',
                        ],
                    ],
                    [
                        'role' => 'Jr. Software Engineer',
                        'start_date' => '2019-07-01',
                        'end_date' => '2021-01-01',
                        'in_progress' => false,
                        'description' => null,
                        'highlights' => [
                            'Maintained multiple software products in different languages and developed integrations with SAP Business One.',
                            'Developed Bolivia\'s new electronic and computerized invoicing (Impuestos Nacionales) with a team.',
                            'Created a SAP B1 Service Layer connection package, reused across many follow-up projects to speed up further development.',
                            'Transformed an ERP into a lightweight e-commerce and built a REST API for an Order mobile app to reach small customers.',
                        ],
                    ],
                ],
            ],
            [
                'company' => 'CenturySoft',
                'summary' => 'Maintenance and development of tracking software.',
                'tech_stack' => ['Laravel', 'PHP', 'Xamarin', '.NET Framework'],
                'sort_order' => 4,
                'levels' => [
                    [
                        'role' => 'Jr. Software Engineer',
                        'start_date' => '2019-01-01',
                        'end_date' => '2019-07-01',
                        'in_progress' => false,
                        'description' => null,
                        'highlights' => [
                            'Maintained the existing tracking platform and developed it forward in Laravel.',
                            'Mobile companion app built with Xamarin.',
                        ],
                    ],
                ],
            ],
            [
                'company' => 'OsBolivia',
                'summary' => 'First software job. Team of seven; no version control at the time, '
                    . 'so maintenance was a complicated job.',
                'tech_stack' => ['.NET Framework', 'Laravel', 'SQL Server'],
                'sort_order' => 5,
                'levels' => [
                    [
                        'role' => 'Trainee Software Engineer',
                        'start_date' => '2017-01-01',
                        'end_date' => '2017-07-01',
                        'in_progress' => false,
                        'description' => null,
                        'highlights' => [
                            'Initialization as a software developer — learned to provide services to business customers.',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($jobs as $job) {
            Experience::updateOrCreate(
                ['owner_id' => $owner->id, 'company' => $job['company']],
                [
                    'summary' => $job['summary'],
                    'tech_stack' => $job['tech_stack'],
                    'sort_order' => $job['sort_order'],
                    'levels' => $job['levels'],
                ],
            );
        }
    }
}
