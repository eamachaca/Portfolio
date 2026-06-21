<?php

namespace App\Filament\Support;

use App\Support\Locales;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;

/**
 * Build a Filament Tabs component with one tab per locale active on the
 * authenticated owner. The factory callback receives the locale code and
 * returns the form components for that tab, with locale-keyed field
 * names (e.g. `title.en`, `bio.es`). Spatie HasTranslations stores those
 * as {"en": "...", "es": "..."} on the model column.
 *
 * Usage:
 *   LocaleTabs::for(fn (string $locale) => [
 *       TextInput::make("headline.{$locale}")->label('Headline'),
 *       Textarea::make("bio.{$locale}")->label('Bio'),
 *   ])
 */
class LocaleTabs
{
    public static function for(callable $fieldsFactory, string $key = 'translations'): Tabs
    {
        $locales = self::activeLocales();

        $tabs = collect($locales)->map(
            fn (string $locale) => Tab::make(Locales::label($locale))
                ->schema($fieldsFactory($locale)),
        )->all();

        return Tabs::make($key)
            ->tabs($tabs)
            ->columnSpanFull();
    }

    /**
     * @return array<int, string>
     */
    private static function activeLocales(): array
    {
        $user = auth()->user();
        $locales = $user?->active_locales;

        return is_array($locales) && $locales !== []
            ? array_values($locales)
            : ['en'];
    }
}
