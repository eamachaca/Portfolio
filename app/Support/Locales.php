<?php

namespace App\Support;

class Locales
{
    /**
     * Locales the platform supports. Used by the profile form, the front
     * switcher and the per-locale Filament Tabs. Add a new entry here and
     * the rest of the system picks it up automatically.
     *
     * @return array<string, string>
     */
    public static function available(): array
    {
        return [
            'en' => 'English',
            'es' => 'Español',
            'pt' => 'Português',
            'fr' => 'Français',
            'de' => 'Deutsch',
            'it' => 'Italiano',
            'nl' => 'Nederlands',
            'ja' => '日本語',
            'zh' => '中文',
            'ko' => '한국어',
            'ru' => 'Русский',
            'ar' => 'العربية',
        ];
    }

    public static function label(string $locale): string
    {
        return self::available()[$locale] ?? strtoupper($locale);
    }

    /**
     * Resolve a translatable value from nested JSON (e.g. levels.*.description,
     * apps.*.description). Accepts either a plain string (returned as-is for
     * back-compat) or a {"en": "...", "es": "..."} map. Falls back through
     * the current app locale → "en" → first non-empty value.
     */
    public static function translate(mixed $value, ?string $locale = null): ?string
    {
        if ($value === null || is_string($value)) {
            return $value;
        }
        if (! is_array($value) || $value === []) {
            return null;
        }
        $locale ??= app()->getLocale();
        if (filled($value[$locale] ?? null)) {
            return $value[$locale];
        }
        if (filled($value['en'] ?? null)) {
            return $value['en'];
        }

        return collect($value)->first(fn ($v) => filled($v));
    }
}
