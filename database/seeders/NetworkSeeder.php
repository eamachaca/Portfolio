<?php

namespace Database\Seeders;

use App\Models\Network;
use Illuminate\Database\Seeder;

class NetworkSeeder extends Seeder
{
    /**
     * Canonical catalog of networks visible to every owner.
     * themify_class is the ti-* class from ReFrame's icon set.
     * Networks without a themify class need an icon_path uploaded
     * from /admin/networks before they render on the front.
     */
    private const CATALOG = [
        ['slug' => 'github',         'name' => 'GitHub',         'themify' => 'ti-github'],
        ['slug' => 'linkedin',       'name' => 'LinkedIn',       'themify' => 'ti-linkedin'],
        ['slug' => 'x',              'name' => 'X',              'themify' => 'ti-twitter-alt'],
        ['slug' => 'facebook',       'name' => 'Facebook',       'themify' => 'ti-facebook'],
        ['slug' => 'instagram',      'name' => 'Instagram',      'themify' => 'ti-instagram'],
        ['slug' => 'youtube',        'name' => 'YouTube',        'themify' => 'ti-youtube'],
        ['slug' => 'twitch',         'name' => 'Twitch',         'themify' => null],
        ['slug' => 'tiktok',         'name' => 'TikTok',         'themify' => null],
        ['slug' => 'dribbble',       'name' => 'Dribbble',       'themify' => 'ti-dribbble'],
        ['slug' => 'behance',        'name' => 'Behance',        'themify' => null],
        ['slug' => 'medium',         'name' => 'Medium',         'themify' => null],
        ['slug' => 'devto',          'name' => 'Dev.to',         'themify' => null],
        ['slug' => 'stackoverflow',  'name' => 'Stack Overflow', 'themify' => null],
        ['slug' => 'mastodon',       'name' => 'Mastodon',       'themify' => null],
        ['slug' => 'bluesky',        'name' => 'Bluesky',        'themify' => null],
        ['slug' => 'discord',        'name' => 'Discord',        'themify' => null],
        ['slug' => 'telegram',       'name' => 'Telegram',       'themify' => null],
        ['slug' => 'whatsapp',       'name' => 'WhatsApp',       'themify' => null],
        ['slug' => 'email',          'name' => 'Email',          'themify' => 'ti-email'],
        ['slug' => 'website',        'name' => 'Website',        'themify' => 'ti-world'],
        ['slug' => 'workana',        'name' => 'Workana',        'themify' => null],
        ['slug' => 'upwork',         'name' => 'Upwork',         'themify' => null],
    ];

    public function run(): void
    {
        foreach (self::CATALOG as $entry) {
            Network::updateOrCreate(
                ['slug' => $entry['slug']],
                [
                    'name' => $entry['name'],
                    'themify_class' => $entry['themify'],
                    'is_approved' => true,
                ],
            );
        }
    }
}
