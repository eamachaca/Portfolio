<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class SetLocale
{
    public const COOKIE = 'locale';

    public const COOKIE_TTL_MINUTES = 60 * 24 * 365;

    public function handle(Request $request, Closure $next)
    {
        $owner = User::query()->first();
        $active = $this->activeLocales($owner);
        $fallback = $this->fallbackLocale($owner, $active);

        $locale = $this->resolve($request, $active, $fallback);
        app()->setLocale($locale);

        $response = $next($request);

        if ($request->cookie(self::COOKIE) !== $locale) {
            $response->headers->setCookie(
                Cookie::create(self::COOKIE, $locale, now()->addMinutes(self::COOKIE_TTL_MINUTES)->getTimestamp())
            );
        }

        return $response;
    }

    private function resolve(Request $request, array $active, string $fallback): string
    {
        $candidates = [
            $request->query('locale'),
            $request->cookie(self::COOKIE),
            $this->fromAcceptLanguage($request, $active),
            $fallback,
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && in_array($candidate, $active, true)) {
                return $candidate;
            }
        }

        return $fallback;
    }

    private function activeLocales(?User $owner): array
    {
        $list = $owner?->active_locales ?? [];
        $list = array_values(array_filter($list, fn ($l) => is_string($l) && $l !== ''));

        if ($list === []) {
            $list = [config('app.locale', 'en')];
        }

        return $list;
    }

    private function fallbackLocale(?User $owner, array $active): string
    {
        $default = $owner?->default_locale;
        if (is_string($default) && in_array($default, $active, true)) {
            return $default;
        }

        return $active[0];
    }

    private function fromAcceptLanguage(Request $request, array $active): ?string
    {
        foreach ($request->getLanguages() as $lang) {
            $short = substr($lang, 0, 2);
            if (in_array($short, $active, true)) {
                return $short;
            }
        }

        return null;
    }
}
