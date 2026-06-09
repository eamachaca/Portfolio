<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::query()->where('username', 'eduardo')->first();

        if (! $owner) {
            return;
        }

        // Drop slugs from earlier iterations where work was mis-modeled as projects.
        Project::query()
            ->whereIn('slug', [
                'hormiguitax',
                'van-scrapper-platform',
                'icorebiz-sap-b1',
                'workana-freelance',
            ])
            ->delete();

        Project::updateOrCreate(
            ['slug' => 'gastos'],
            [
                'owner_id' => $owner->id,
                'experience_id' => null, // Personal project, no employer link.
                'title' => 'Gastos',
                'excerpt' => 'Personal-finance product shipped as HormiguitaX — multi-currency web panel + Flutter mobile.',
                'description' => 'Codebase: Gastos. Product brand: HormiguitaX. '
                    . 'Personal-finance management with wallets across currencies, '
                    . 'transfers and cross-currency exchanges with bidirectional calculator, '
                    . 'loans and debts with compound interest, credit purchases by installment, '
                    . 'recurring payments with reminders, and a daily cron that accrues interest, '
                    . 'flags overdue charges and emits events for push notifications. '
                    . 'Multi-user with invite-link signup, manual approval, admin/user roles, '
                    . 'runtime brand color, ES/EN and light/dark. '
                    . 'Backend live on staging; mobile coded, visual verification in progress.',
                'cover_image' => null,
                'gallery' => [],
                'tech_stack' => [
                    'Laravel', 'Filament', 'PHP', 'PostgreSQL',
                    'Sanctum', 'Flutter', 'Dart', 'Drift',
                ],
                'apps' => [
                    [
                        'name' => 'HormiguitaX Web (Backend & Admin Panel)',
                        'platform' => 'Web',
                        'description' => 'Laravel backend with a Filament admin panel as the main UI today. '
                            . 'Login by username with invite-link signup and manual approval, admin/user roles, '
                            . 'runtime-editable brand color, ES/EN, light/dark. Exposes the full feature set '
                            . '(wallets, movements, loans, credit purchases, recurring payments, counterparts, '
                            . 'currencies). REST API on top with Sanctum bearer auth, cursor pagination, '
                            . 'idempotency keys and ~75 green tests.',
                        'tech_stack' => ['Laravel', 'Filament', 'PHP', 'PostgreSQL', 'Sanctum'],
                        'links' => [
                            'UAT' => 'https://staging.gastos.deito.dev',
                        ],
                    ],
                    [
                        'name' => 'HormiguitaX Mobile',
                        'platform' => 'iOS · Android',
                        'description' => 'Flutter app with a 4-tab shell visible signed-in or signed-out. '
                            . 'Dashboard with breakdown by currency, full CRUD over wallets, movements, '
                            . 'loans (grouped by counterpart with consolidated net balance), credit purchases, '
                            . 'recurring payments, counterparts. Profile, preferences, language and active sessions '
                            . 'under a More hub. Drift for local storage, Keychain/Keystore for tokens, '
                            . 'HTTP interceptors for token + language.',
                        'tech_stack' => ['Flutter', 'Dart', 'Drift'],
                        'links' => [],
                    ],
                ],
                'url' => null,
                'repo_url' => null,
                'featured' => true,
                'sort_order' => 1,
                'published_at' => now(),
            ],
        );
    }
}
