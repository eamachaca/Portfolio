<?php

namespace Database\Seeders;

use App\Models\Network;
use App\Models\SocialLink;
use App\Models\User;
use Illuminate\Database\Seeder;

class SocialLinkSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->where('username', 'eduardo')->first();

        if (! $user) {
            return;
        }

        $links = [
            'github'   => 'https://github.com/eamachaca',
            'linkedin' => 'https://www.linkedin.com/in/eamachaca',
            'workana'  => 'https://www.workana.com/freelancer/2dabb583384666d5ff6a1ecc0208bd69',
        ];

        $sort = 0;

        foreach ($links as $slug => $url) {
            $network = Network::query()->where('slug', $slug)->first();

            if (! $network) {
                continue;
            }

            SocialLink::updateOrCreate(
                ['user_id' => $user->id, 'network_id' => $network->id],
                ['url' => $url, 'sort_order' => $sort++],
            );
        }
    }
}
