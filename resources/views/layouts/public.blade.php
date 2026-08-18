<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $siteSettings->company_name ?: 'Grupo Edima')</title>
    <meta name="description" content="@yield('meta_description', $siteSettings->getTranslation('meta_description', app()->getLocale()))">

    <link rel="icon" href="{{ asset('favicon.ico') }}">
    <link rel="canonical" href="{{ url()->current() }}">
    @foreach ($localeUrls as $locale => $url)
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ $url }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $localeUrls[config('site.default_locale')] }}">

    {{--
        Open Graph / Twitter Card. Cada vista de detalle puede sobrescribir
        og_title, og_description y og_image (ver public/services/show.blade.php
        y similares); si no lo hace, cae al título/descripción de la página y
        al logo de marca.
    --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:site_name" content="{{ $siteSettings->company_name ?: 'Grupo Edima' }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'es' ? 'es_CO' : 'en_US' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('og_title', $__env->yieldContent('title', $siteSettings->company_name ?: 'Grupo Edima'))">
    <meta property="og:description" content="@yield('og_description', $__env->yieldContent('meta_description', $siteSettings->getTranslation('meta_description', app()->getLocale())))">
    <meta property="og:image" content="@yield('og_image', asset('images/brand/logo-grupo-edima-blanco.png'))">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', $__env->yieldContent('title', $siteSettings->company_name ?: 'Grupo Edima'))">
    <meta name="twitter:description" content="@yield('og_description', $__env->yieldContent('meta_description', $siteSettings->getTranslation('meta_description', app()->getLocale())))">
    <meta name="twitter:image" content="@yield('og_image', asset('images/brand/logo-grupo-edima-blanco.png'))">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="overflow-x-hidden bg-brand-neutral-50 text-brand-neutral-900 font-body antialiased">

    <a href="#contenido" class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:m-2 focus:rounded focus:bg-brand-primary-700 focus:px-4 focus:py-2 focus:text-white">
        {{ app()->getLocale() === 'es' ? 'Saltar al contenido' : 'Skip to content' }}
    </a>

    <header x-data="{ open: false }" class="relative border-b border-brand-neutral-200 bg-brand-neutral-50">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-6 py-4">
            <a href="{{ route(app()->getLocale().'.home') }}" class="flex items-center gap-2.5 shrink-0">
                <img src="{{ asset('images/brand/icono-grupo-edima.ico') }}" width="34" height="34" alt="Grupo Edima">
                <span class="font-body text-base font-bold tracking-wide text-brand-primary-700">GRUPO EDIMA</span>
            </a>

            <nav class="hidden items-center gap-7 font-body text-sm font-semibold text-brand-neutral-700 md:flex">
                @foreach ($navItems as $item)
                    <a href="{{ $item->url }}"
                       class="transition-colors hover:text-brand-primary-700 {{ request()->is(ltrim((string) parse_url($item->url, PHP_URL_PATH), '/')) ? 'text-brand-primary-700' : '' }}">
                        {{ $item->label }}
                    </a>
                @endforeach
            </nav>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-1 font-body text-sm font-semibold">
                    @foreach (config('site.locales') as $locale => $label)
                        <a href="{{ $localeUrls[$locale] }}"
                           aria-label="{{ __('site.language_switcher') }}: {{ $label }}"
                           aria-current="{{ app()->getLocale() === $locale ? 'true' : 'false' }}"
                           class="flex h-9 min-w-9 items-center justify-center rounded-sm px-2 uppercase {{ app()->getLocale() === $locale ? 'bg-brand-primary-700 text-white' : 'text-brand-neutral-600 hover:text-brand-primary-700' }}">
                            {{ $locale }}
                        </a>
                    @endforeach
                </div>

                <a href="{{ route(app()->getLocale().'.contact') }}" class="btn-primary hidden px-5 py-2.5 text-sm sm:inline-flex">
                    {{ __('site.actions.contact_us') }}
                </a>

                <button @click="open = !open" type="button"
                        class="p-2.5 text-brand-neutral-700 md:hidden"
                        :aria-expanded="open.toString()"
                        aria-label="{{ app()->getLocale() === 'es' ? 'Abrir menú' : 'Open menu' }}">
                    <svg x-cloak x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-cloak x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div x-cloak x-show="open" x-transition @click.outside="open = false"
             class="absolute inset-x-0 top-full z-40 border-b border-brand-neutral-200 bg-brand-neutral-50 shadow-md md:hidden">
            <nav class="mx-auto flex max-w-6xl flex-col gap-1 px-6 py-4 font-body text-sm font-semibold text-brand-neutral-700">
                @foreach ($navItems as $item)
                    <a href="{{ $item->url }}" @click="open = false"
                       class="rounded px-2 py-2.5 transition-colors hover:bg-brand-primary-50 hover:text-brand-primary-700 {{ request()->is(ltrim((string) parse_url($item->url, PHP_URL_PATH), '/')) ? 'text-brand-primary-700' : '' }}">
                        {{ $item->label }}
                    </a>
                @endforeach
                <a href="{{ route(app()->getLocale().'.contact') }}" @click="open = false"
                   class="btn-primary mt-2 justify-center">
                    {{ __('site.actions.contact_us') }}
                </a>
            </nav>
        </div>
    </header>

    <main id="contenido">
        @yield('content')
    </main>

    <footer class="bg-brand-neutral-900 text-brand-neutral-300">
        <div class="mx-auto grid max-w-6xl gap-10 px-6 py-12 md:grid-cols-[1.4fr_1fr_1fr]">
            <div>
                <img src="{{ asset('images/brand/logo-grupo-edima-blanco.png') }}" alt="Grupo Edima" class="h-9 w-auto">
                <p class="mt-4 max-w-xs font-body text-sm text-brand-neutral-400">
                    {{ $siteSettings->getTranslation('footer_text', app()->getLocale()) ?: ($siteSettings->company_name ?: 'Grupo Edima') }}
                </p>

                @if ($siteSettings->social_links)
                    <div class="mt-5 flex items-center gap-4">
                        @foreach ($siteSettings->social_links as $social)
                            <a href="{{ $social['url'] ?? '#' }}" target="_blank" rel="noopener"
                               class="text-brand-neutral-300 transition-colors hover:text-brand-accent-300"
                               aria-label="{{ ucfirst($social['platform'] ?? '') }}">
                                {{ ucfirst($social['platform'] ?? '') }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div>
                <h4 class="mb-3 font-body text-xs font-semibold uppercase tracking-wider text-brand-neutral-50">
                    {{ __('site.footer.services_heading') }}
                </h4>
                <ul class="flex flex-col gap-2 font-body text-sm">
                    <li><a href="{{ route(app()->getLocale().'.solutions') }}" class="hover:text-brand-accent-300">{{ __('site.nav.solutions') }}</a></li>
                    <li><a href="{{ route(app()->getLocale().'.services') }}" class="hover:text-brand-accent-300">{{ __('site.nav.services') }}</a></li>
                    <li><a href="{{ route(app()->getLocale().'.projects') }}" class="hover:text-brand-accent-300">{{ __('site.nav.projects') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="mb-3 font-body text-xs font-semibold uppercase tracking-wider text-brand-neutral-50">
                    {{ __('site.footer.company_heading') }}
                </h4>
                <ul class="flex flex-col gap-2 font-body text-sm">
                    <li><a href="{{ route(app()->getLocale().'.about') }}" class="hover:text-brand-accent-300">{{ __('site.nav.about') }}</a></li>
                    <li><a href="{{ route(app()->getLocale().'.contact') }}" class="hover:text-brand-accent-300">{{ __('site.nav.contact') }}</a></li>
                    @if ($siteSettings->phone)
                        <li>{{ $siteSettings->phone }}</li>
                    @endif
                    @if ($siteSettings->email)
                        <li><a href="mailto:{{ $siteSettings->email }}" class="hover:text-brand-accent-300">{{ $siteSettings->email }}</a></li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="border-t border-brand-neutral-800 px-6 py-5 text-center font-body text-xs text-brand-neutral-500">
            &copy; {{ now()->year }} {{ $siteSettings->company_name ?: 'Grupo Edima' }}. {{ __('site.footer.rights') }}
        </div>
    </footer>

</body>
</html>
