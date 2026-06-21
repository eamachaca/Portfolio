<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Maps known legacy keys to canonical name + themify class.
     * Keys not listed are migrated as is_approved=true with no icon
     * (admin can upload one later from /admin/networks).
     */
    private const KNOWN = [
        'github'    => ['name' => 'GitHub',    'themify' => 'ti-github'],
        'linkedin'  => ['name' => 'LinkedIn',  'themify' => 'ti-linkedin'],
        'twitter'   => ['name' => 'X',         'themify' => 'ti-twitter-alt'],
        'x'         => ['name' => 'X',         'themify' => 'ti-twitter-alt'],
        'facebook'  => ['name' => 'Facebook',  'themify' => 'ti-facebook'],
        'instagram' => ['name' => 'Instagram', 'themify' => 'ti-instagram'],
        'youtube'   => ['name' => 'YouTube',   'themify' => 'ti-youtube'],
        'dribbble'  => ['name' => 'Dribbble',  'themify' => 'ti-dribbble'],
        'behance'   => ['name' => 'Behance',   'themify' => null],
        'medium'    => ['name' => 'Medium',    'themify' => null],
        'email'     => ['name' => 'Email',     'themify' => 'ti-email'],
    ];

    public function up(): void
    {
        $users = DB::table('users')->whereNotNull('social_links')->get(['id', 'social_links']);

        foreach ($users as $user) {
            $links = json_decode($user->social_links, true) ?: [];
            $sort = 0;

            foreach ($links as $key => $url) {
                if (! is_string($key) || ! is_string($url) || $url === '') {
                    continue;
                }

                $slug = Str::slug($key);
                $meta = self::KNOWN[strtolower($key)] ?? null;

                $networkId = DB::table('networks')->where('slug', $slug)->value('id');

                if (! $networkId) {
                    $networkId = DB::table('networks')->insertGetId([
                        'slug' => $slug,
                        'name' => $meta['name'] ?? Str::title(str_replace('-', ' ', $slug)),
                        'themify_class' => $meta['themify'] ?? null,
                        'icon_path' => null,
                        'is_approved' => true,
                        'merged_into_id' => null,
                        'created_by' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                DB::table('social_links')->updateOrInsert(
                    ['user_id' => $user->id, 'network_id' => $networkId],
                    ['url' => $url, 'sort_order' => $sort++, 'updated_at' => now(), 'created_at' => now()],
                );
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('social_links');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('social_links')->nullable();
        });

        $rows = DB::table('social_links')
            ->join('networks', 'networks.id', '=', 'social_links.network_id')
            ->select('social_links.user_id', 'networks.slug', 'social_links.url')
            ->orderBy('social_links.user_id')
            ->orderBy('social_links.sort_order')
            ->get();

        $byUser = $rows->groupBy('user_id');

        foreach ($byUser as $userId => $userRows) {
            $payload = $userRows->mapWithKeys(fn ($r) => [$r->slug => $r->url])->all();
            DB::table('users')->where('id', $userId)->update(['social_links' => json_encode($payload)]);
        }

        DB::table('social_links')->delete();
        DB::table('networks')->delete();
    }
};
