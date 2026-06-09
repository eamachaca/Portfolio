<?php

namespace Database\Seeders;

use App\Models\Study;
use App\Models\User;
use Illuminate\Database\Seeder;

class StudySeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::query()->where('username', 'eduardo')->first();

        if (! $owner) {
            return;
        }

        Study::updateOrCreate(
            [
                'owner_id' => $owner->id,
                'institution' => 'Universidad Autónoma Gabriel René Moreno',
                'title' => 'Bachelor of Informatics Engineering',
            ],
            [
                'field' => 'Informatics Engineering',
                'description' => 'Bachelor degree in Informatics Engineering. '
                    . 'Universidad Autónoma Gabriel René Moreno (UAGRM), '
                    . 'Santa Cruz de la Sierra, Bolivia.',
                'start_date' => null,
                'end_date' => '2020-12-01',
                'in_progress' => false,
                'logo' => null,
                'sort_order' => 1,
            ],
        );
    }
}
