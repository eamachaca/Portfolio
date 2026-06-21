@php
    $owner = auth()->user() ?? \App\Models\User::query()->first();
    $activeLocales = array_values(array_filter($owner?->active_locales ?? [], fn ($l) => is_string($l) && $l !== ''));
    $currentLocale = app()->getLocale();
@endphp

@if(count($activeLocales) > 1)
    <ul class="fi-admin-lang-switch">
        @foreach($activeLocales as $code)
            <li class="{{ $code === $currentLocale ? 'is-active' : '' }}">
                <a href="{{ request()->fullUrlWithQuery(['locale' => $code]) }}">{{ strtoupper($code) }}</a>
            </li>
        @endforeach
    </ul>

    <style>
        .fi-admin-lang-switch {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.75rem;
            letter-spacing: 0.12em;
            font-weight: 600;
            margin-right: 0.5rem;
        }
        .fi-admin-lang-switch li a {
            color: rgb(115 115 115);
            text-decoration: none;
            padding: 0.25rem 0;
            border-bottom: 1px solid transparent;
            transition: color .15s ease, border-color .15s ease;
        }
        .fi-admin-lang-switch li.is-active a,
        .fi-admin-lang-switch li:hover a {
            color: rgb(245 158 11);
            border-bottom-color: rgb(245 158 11);
        }
        .dark .fi-admin-lang-switch li a {
            color: rgb(163 163 163);
        }
    </style>
@endif
