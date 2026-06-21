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
                'headline' => ['en' => 'Backend Developer', 'es' => 'Desarrollador Backend'],
                'bio' => [
                    'en' => 'Software Developer with 6+ years of experience developing web applications '
                        . 'using PHP (Laravel), C#, RoR and Services (SOAP & REST), with a focus on '
                        . 'process improvement and automation. Technology enthusiast and learning '
                        . 'fanatic — there should never be excuses to learn something new. Based in '
                        . 'Santa Cruz, Bolivia (GMT-4).',
                    'es' => 'Desarrollador de Software con 6+ años de experiencia construyendo aplicaciones '
                        . 'web en PHP (Laravel), C#, RoR y Servicios (SOAP & REST), con foco en mejora '
                        . 'de procesos y automatización. Entusiasta de la tecnología y fanático del '
                        . 'aprendizaje continuo. Radicado en Santa Cruz, Bolivia (GMT-4).',
                ],
                'avatar' => 'avatars/eduardo-machaca.jpg',
                'active_locales' => ['en', 'es'],
                'default_locale' => 'en',
                'email_verified_at' => now(),

                'hero_tag' => 'WEB APPS / LARAVEL / AUTOMATION',
                'hero_title' => [
                    'en' => 'Clean web apps. Real problems solved.',
                    'es' => 'Apps web prolijas. Problemas reales resueltos.',
                ],
                'hero_copy' => [
                    'en' => 'Laravel, automation, APIs, dashboards, and practical interfaces for how people actually work.',
                    'es' => 'Laravel, automatización, APIs, dashboards e interfaces prácticas para cómo trabaja la gente realmente.',
                ],
                'hero_note' => [
                    'en' => 'Backend-heavy when needed, interface-aware always.',
                    'es' => 'Backend-heavy cuando hace falta, consciente de la interfaz siempre.',
                ],

                'about_heading' => [
                    'en' => 'Practical code, usable products.',
                    'es' => 'Código práctico, productos usables.',
                ],
                'about_body' => [
                    'en' => "I'm Eduardo, a web developer based in Santa Cruz, Bolivia. My strongest work is Laravel and backend-heavy systems, but I also care about the interface, the flow, and whether the final product feels clear.\n\nI enjoy understanding messy systems, improving the parts that slow teams down, and turning technical requirements into tools that people can understand and use.",
                    'es' => "Soy Eduardo, desarrollador web radicado en Santa Cruz, Bolivia. Mi trabajo más fuerte es Laravel y sistemas backend-heavy, pero también me importa la interfaz, el flujo y si el producto final se entiende.\n\nDisfruto entender sistemas desordenados, mejorar las partes que ralentizan a los equipos y convertir requerimientos técnicos en herramientas que la gente puede entender y usar.",
                ],

                'strengths_heading' => [
                    'en' => 'What I bring to a project.',
                    'es' => 'Lo que aporto en un proyecto.',
                ],

                'experience_heading' => [
                    'en' => 'Real systems. Useful products.',
                    'es' => 'Sistemas reales. Productos útiles.',
                ],
                'experience_intro' => [
                    'en' => 'A timeline of web development, integrations, support, and production problem-solving.',
                    'es' => 'Una línea de tiempo de desarrollo web, integraciones, soporte y resolución de problemas en producción.',
                ],

                'portfolio_heading' => [
                    'en' => 'Selected product work.',
                    'es' => 'Trabajo de producto seleccionado.',
                ],
                'portfolio_intro' => [
                    'en' => 'A few examples of product thinking, Laravel work, automation, and interfaces built around real needs.',
                    'es' => 'Algunos ejemplos de pensamiento de producto, trabajo en Laravel, automatización e interfaces armadas alrededor de necesidades reales.',
                ],

                'skills_heading' => [
                    'en' => 'A stack that gets work done.',
                    'es' => 'Un stack que entrega resultados.',
                ],
                'skills_intro' => [
                    'en' => 'Tools I reach for when a project needs a stable app, a clear interface, an integration, or a stubborn production issue solved.',
                    'es' => 'Herramientas que uso cuando un proyecto necesita una app estable, una interfaz clara, una integración o resolver un problema terco en producción.',
                ],

                'workstyle_heading' => [
                    'en' => 'Clear, practical, comfortable with the messy parts.',
                    'es' => 'Claro, práctico, cómodo con la parte sucia.',
                ],

                'faq_heading' => [
                    'en' => 'A few practical details.',
                    'es' => 'Algunos detalles prácticos.',
                ],

                'blog_heading' => [
                    'en' => 'Notes from the workbench.',
                    'es' => 'Notas desde el taller.',
                ],

                'contact_heading' => [
                    'en' => 'Have a product or workflow worth improving?',
                    'es' => '¿Tenés un producto o workflow que valga la pena mejorar?',
                ],
                'contact_intro' => [
                    'en' => 'Send a short message about the product, bug, integration, dashboard, or automation idea. I will read it like a real person.',
                    'es' => 'Mandá un mensaje corto sobre el producto, bug, integración, dashboard o idea de automatización. Lo leo como persona real, no como ticket.',
                ],
            ],
        );
    }
}
