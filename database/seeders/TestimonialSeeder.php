<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::query()->where('username', 'eduardo')->first();

        if (! $owner) {
            return;
        }

        $workanaProfile = 'https://www.workana.com/freelancer/2dabb583384666d5ff6a1ecc0208bd69';

        $testimonials = [
            [
                'author' => 'Valeria Matamoros',
                'role' => 'Cliente recurrente',
                'company' => 'Contratación directa',
                'avatar' => 'testimonials/valeria-matamoros.jpeg',
                'source' => 'workana',
                'source_url' => $workanaProfile,
                'quote' => 'Es la cuarta vez que lo contrato para unos proyectos, altamente recomendado. '
                    . 'Excelente comunicación y los trabajos son de calidad.',
            ],
            [
                'author' => 'Marlene Melgar',
                'role' => 'Cliente',
                'company' => 'Modificaciones a página PHP',
                'avatar' => 'testimonials/marlene-melgar.jpg',
                'source' => 'workana',
                'source_url' => $workanaProfile,
                'quote' => 'Altamente recomendado, excelente comunicación, entrega en tiempo y su trabajo '
                    . 'tiene una calidad muy difícil de superar. ¡Simplemente excelente!',
            ],
            [
                'author' => 'José Tostado',
                'role' => 'Cliente',
                'company' => 'Desarrollo de adicionales en Laravel',
                'avatar' => 'testimonials/jose-tostado.jpg',
                'source' => 'workana',
                'source_url' => $workanaProfile,
                'quote' => 'La disposición de Eduardo para trabajar, involucrarse en el proyecto y resolver '
                    . 'todos los obstáculos hasta completar la tarea es genial. Muy recomendable trabajar con él.',
            ],
            [
                'author' => 'José Angel',
                'role' => 'Cliente',
                'company' => 'Experto en Laravel para ajustes a sistema existente',
                'avatar' => 'testimonials/jose-angel.jpg',
                'source' => 'workana',
                'source_url' => $workanaProfile,
                'quote' => 'Eduardo ha hecho un gran trabajo en este proyecto, muy recomendable. '
                    . '¡Gran aporte a nuestro equipo!',
            ],
            [
                'author' => 'Pedro',
                'role' => 'Cliente',
                'company' => 'Cambios para proyecto en Laravel y Angular',
                'avatar' => 'testimonials/pedro.jpg',
                'source' => 'workana',
                'source_url' => $workanaProfile,
                'quote' => 'Excelente profesional, dedicado y 100% enfocado a la resolución de lo pedido, '
                    . 'altamente recomendable.',
            ],
            [
                'author' => 'Sergio Cerecer',
                'role' => 'Cliente',
                'company' => 'Modificar reporte en PHP',
                'avatar' => 'testimonials/sergio-cerecer.png',
                'source' => 'workana',
                'source_url' => $workanaProfile,
                'quote' => 'Excelente programador para el reporte que necesitaba. Lo hizo tal cual. '
                    . 'Lo recomiendo ampliamente.',
            ],
            [
                'author' => 'José Tostado',
                'role' => 'Cliente',
                'company' => 'Despliegue Laravel en DigitalOcean',
                'avatar' => 'testimonials/jose-tostado.jpg',
                'source' => 'workana',
                'source_url' => $workanaProfile,
                'quote' => 'Muy atento, amable y dispuesto a apoyar, con buena habilidad técnica para '
                    . 'cumplir el proyecto, muy recomendable trabajar con Andrés.',
            ],
            [
                'author' => 'Catalina Hernandez',
                'role' => 'Cliente',
                'company' => 'Resolver error de envío de correos',
                'avatar' => 'testimonials/catalina-hernandez.jpg',
                'source' => 'workana',
                'source_url' => $workanaProfile,
                'quote' => 'Conoce sobre el tema, trabajo muy responsable hasta que lo solucionó.',
            ],
            [
                'author' => 'Gustavo Suarez',
                'role' => 'Cliente',
                'company' => 'Ajustar web existente en Laravel',
                'avatar' => null,
                'source' => 'workana',
                'source_url' => $workanaProfile,
                'quote' => 'Excelente trabajo, rápido y eficiente. Súper recomendable.',
            ],
            [
                'author' => 'JOAN',
                'role' => 'Cliente',
                'company' => 'Importación de proyecto Laravel a local',
                'avatar' => 'testimonials/joan.png',
                'source' => 'workana',
                'source_url' => $workanaProfile,
                'quote' => 'Muy buen trabajo, muy recomendable...',
            ],
            [
                'author' => 'fernando perez',
                'role' => 'Cliente',
                'company' => 'Modificación de aplicación móvil',
                'avatar' => null,
                'source' => 'workana',
                'source_url' => $workanaProfile,
                'quote' => 'Súper recomendado.',
            ],
        ];

        foreach ($testimonials as $i => $t) {
            Testimonial::updateOrCreate(
                [
                    'owner_id' => $owner->id,
                    'author' => $t['author'],
                    'company' => $t['company'],
                ],
                [
                    'role' => $t['role'],
                    'quote' => $t['quote'],
                    'avatar' => $t['avatar'],
                    'source' => $t['source'],
                    'source_url' => $t['source_url'],
                    'sort_order' => $i + 1,
                ],
            );
        }
    }
}
