@extends('layouts.public')

@section('title', ($page?->getTranslation('meta_title', app()->getLocale())) ?: (($page->title ?? 'Grupo Edima').' · Grupo Edima'))
@section('meta_description', $page?->getTranslation('meta_description', app()->getLocale()) ?: ($siteSettings->getTranslation('meta_description', app()->getLocale()) ?: ''))
@if ($page?->getFirstMediaUrl(\App\Models\Page::COVER, \App\Support\ImageConversions::WEB))
    @section('og_image', $page->getFirstMediaUrl(\App\Models\Page::COVER, \App\Support\ImageConversions::WEB))
@endif

@php
    // Bloques de la página de inicio, definidos en el panel (Page::HOME → sections).
    $s = is_array($page?->sections) ? $page->sections : [];
@endphp

@section('content')
    {{-- Hero: un solo mensaje. Reemplaza el carrusel de 3 slides del sitio anterior. --}}
    <section class="relative overflow-hidden bg-brand-primary-900 px-6 py-20 text-center text-white sm:py-24">
        <img src="{{ asset('images/illustrations/pages/home-hero.svg') }}" alt=""
             class="pointer-events-none absolute inset-0 h-full w-full object-cover opacity-70">
        <div class="relative mx-auto max-w-3xl">
            @if (data_get($s, 'hero.eyebrow'))
                <p class="mb-3 font-body text-xs font-semibold tracking-widest text-brand-accent-300 uppercase">
                    {{ data_get($s, 'hero.eyebrow') }}
                </p>
            @endif

            <h1 class="font-heading text-4xl font-semibold text-white sm:text-5xl">
                {{ $page->title ?? 'Grupo Edima' }}
            </h1>

            @if (data_get($s, 'hero.subtitle') ?? $page?->subtitle)
                <p class="mx-auto mt-5 max-w-2xl font-body text-base text-brand-secondary-200 sm:text-lg">
                    {{ data_get($s, 'hero.subtitle') ?? $page->subtitle }}
                </p>
            @endif

            <div class="mt-9 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route(app()->getLocale().'.contact') }}" class="btn-primary bg-white text-brand-primary-800 hover:bg-brand-neutral-100">
                    {{ data_get($s, 'hero.cta_primary') ?? __('site.actions.contact_us') }}
                </a>
                <a href="{{ route(app()->getLocale().'.services') }}" class="btn-secondary-inverse">
                    {{ data_get($s, 'hero.cta_secondary') ?? __('site.nav.services') }}
                </a>
            </div>
        </div>
    </section>

    {{-- Introducción --}}
    @if (data_get($s, 'intro.body'))
        <section class="border-b border-brand-neutral-200 bg-white px-6 py-16 sm:py-20">
            <div class="mx-auto max-w-3xl text-center">
                <h2 class="font-heading text-2xl font-semibold text-brand-neutral-900 sm:text-3xl">
                    {{ data_get($s, 'intro.heading') }}
                </h2>
                <p class="mt-4 font-body leading-relaxed text-brand-neutral-700">
                    {{ data_get($s, 'intro.body') }}
                </p>
            </div>
        </section>
    @endif

    {{-- Servicios --}}
    <section class="border-t border-brand-neutral-200 bg-brand-neutral-100 px-6 py-16 sm:py-20">
        <div class="mx-auto max-w-6xl">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="font-heading text-2xl font-semibold text-brand-neutral-900 sm:text-3xl">
                    {{ data_get($s, 'services_summary.heading') ?? __('site.nav.services') }}
                </h2>
                @if (data_get($s, 'services_summary.subheading'))
                    <p class="mt-3 font-body text-brand-neutral-600">{{ data_get($s, 'services_summary.subheading') }}</p>
                @endif
            </div>

            @if ($services->isEmpty())
                <div class="mt-10">
                    @include('public.partials.empty-state', ['message' => __('site.empty.services')])
                </div>
            @else
                <div class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($services as $service)
                        @include('public.partials.content-card', [
                            'image' => $service->getFirstMediaUrl(\App\Models\Service::IMAGE, \App\Support\ImageConversions::WEB) ?: \App\Support\Illustrations::forService($service),
                            'badge' => null,
                            'title' => $service->title,
                            'description' => $service->summary,
                            'href' => route(app()->getLocale().'.services.show', $service),
                            'linkLabel' => __('site.actions.read_more'),
                        ])
                    @endforeach
                </div>

                <div class="mt-10 text-center">
                    <a href="{{ route(app()->getLocale().'.services') }}" class="btn-secondary">
                        {{ __('site.actions.view_all_services') }}
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- ¿Estás evaluando HOPEX? --}}
    @if (data_get($s, 'hopex_teaser.body'))
        <section class="border-t border-brand-neutral-200 bg-white px-6 py-16">
            <div class="mx-auto flex max-w-4xl flex-col items-center gap-6 text-center sm:flex-row sm:text-left">
                <div class="flex-1">
                    <h2 class="font-heading text-xl font-semibold text-brand-neutral-900 sm:text-2xl">
                        {{ data_get($s, 'hopex_teaser.heading') }}
                    </h2>
                    <p class="mt-3 font-body text-brand-neutral-600">{{ data_get($s, 'hopex_teaser.body') }}</p>
                </div>
                <a href="{{ route(app()->getLocale().'.hopex') }}" class="btn-secondary shrink-0">
                    {{ data_get($s, 'hopex_teaser.cta') ?? __('site.nav.hopex') }}
                </a>
            </div>
        </section>
    @endif

    {{-- Cómo trabajamos --}}
    @if (data_get($s, 'approach.items'))
        <section class="border-t border-brand-neutral-200 bg-brand-neutral-100 px-6 py-16 sm:py-20">
            <div class="mx-auto max-w-5xl">
                <h2 class="text-center font-heading text-2xl font-semibold text-brand-neutral-900 sm:text-3xl">
                    {{ data_get($s, 'approach.heading') }}
                </h2>
                <div class="mt-10 grid gap-6 md:grid-cols-3">
                    @foreach (data_get($s, 'approach.items') as $item)
                        <div class="rounded-lg border border-brand-neutral-200 bg-white p-6 shadow-sm">
                            <h3 class="font-heading text-lg font-semibold text-brand-neutral-900">{{ $item['title'] }}</h3>
                            <p class="mt-3 font-body text-sm leading-relaxed text-brand-neutral-600">{{ $item['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Soluciones --}}
    @if (data_get($s, 'capabilities.items'))
        <section class="border-t border-brand-neutral-200 bg-white px-6 py-16 sm:py-20">
            <div class="mx-auto max-w-5xl">
                <h2 class="text-center font-heading text-2xl font-semibold text-brand-neutral-900 sm:text-3xl">
                    {{ data_get($s, 'capabilities.heading') }}
                </h2>
                @if (data_get($s, 'capabilities.subheading'))
                    <p class="mx-auto mt-3 max-w-2xl text-center font-body text-brand-neutral-600">{{ data_get($s, 'capabilities.subheading') }}</p>
                @endif
                <div class="mt-10 grid gap-x-10 gap-y-8 sm:grid-cols-2">
                    @foreach (data_get($s, 'capabilities.items') as $item)
                        @php $slug = $item['slug'] ?? null; @endphp
                        @if ($slug)
                            <a href="{{ route(app()->getLocale().'.solutions.show', $slug) }}"
                               class="group border-l-2 border-brand-accent-400 pl-5 transition-colors hover:border-brand-primary-700">
                                <h3 class="font-heading text-base font-semibold text-brand-neutral-900 group-hover:text-brand-primary-700">{{ $item['title'] }}</h3>
                                <p class="mt-2 font-body text-sm text-brand-neutral-600">{{ $item['description'] }}</p>
                            </a>
                        @else
                            <div class="border-l-2 border-brand-accent-400 pl-5">
                                <h3 class="font-heading text-base font-semibold text-brand-neutral-900">{{ $item['title'] }}</h3>
                                <p class="mt-2 font-body text-sm text-brand-neutral-600">{{ $item['description'] }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="mt-10 text-center">
                    <a href="{{ route(app()->getLocale().'.solutions') }}" class="btn-secondary">
                        {{ __('site.actions.view_all_solutions') }}
                    </a>
                </div>
            </div>
        </section>
    @endif

    {{-- Clientes: sólo si el bloque está marcado como publicado en el panel
         (requiere autorización de uso de marca de cada organización). --}}
    @if (data_get($s, 'clients.published') && data_get($s, 'clients.items'))
        <section class="border-t border-brand-neutral-200 bg-brand-neutral-100 px-6 py-14">
            <div class="mx-auto max-w-5xl text-center">
                <h2 class="font-heading text-xl font-semibold text-brand-neutral-900">{{ data_get($s, 'clients.heading') }}</h2>
                <div class="mt-8 flex flex-wrap items-center justify-center gap-x-10 gap-y-6">
                    @foreach (data_get($s, 'clients.items') as $client)
                        <img src="{{ asset($client['logo']) }}" alt="{{ $client['name'] }}"
                             class="h-10 w-auto opacity-70 grayscale transition hover:opacity-100 hover:grayscale-0">
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Cifras: sólo si el bloque está publicado (requiere datos reales). --}}
    @if (data_get($s, 'facts.published') && data_get($s, 'facts.items'))
        <section class="border-t border-brand-neutral-200 bg-brand-primary-800 px-6 py-14 text-white">
            <div class="mx-auto grid max-w-5xl gap-8 text-center sm:grid-cols-2 lg:grid-cols-4">
                @foreach (data_get($s, 'facts.items') as $fact)
                    <div>
                        <p class="font-heading text-3xl font-semibold text-brand-accent-300">{{ $fact['value'] }}</p>
                        <p class="mt-1 font-body text-sm text-brand-secondary-200">{{ $fact['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Testimonios --}}
    @if ($testimonials->isNotEmpty())
        <section class="border-t border-brand-neutral-200 bg-brand-neutral-100 px-6 py-16 sm:py-20"
                 x-data="{ index: 0 }">
            <div class="mx-auto max-w-3xl text-center">
                <h2 class="font-heading text-2xl font-semibold text-brand-neutral-900 sm:text-3xl">
                    {{ __('site.home.testimonials_heading') }}
                </h2>

                <div class="relative mt-10 overflow-hidden">
                    <div class="flex transition-transform duration-500 ease-out" :style="`transform: translateX(-${index * 100}%)`">
                        @foreach ($testimonials as $testimonial)
                            <div class="w-full shrink-0 px-2">
                                <blockquote class="rounded-lg border border-brand-neutral-200 bg-white p-8 shadow-sm">
                                    <p class="font-heading text-lg text-brand-neutral-800 italic">
                                        &ldquo;{{ $testimonial->quote }}&rdquo;
                                    </p>
                                    <footer class="mt-6 flex items-center justify-center gap-3">
                                        @php $photo = $testimonial->getFirstMediaUrl(\App\Models\Testimonial::PHOTO, \App\Support\ImageConversions::THUMB); @endphp
                                        @if ($photo)
                                            <img src="{{ $photo }}" alt="{{ $testimonial->author_name }}" class="h-10 w-10 rounded-full object-cover">
                                        @endif
                                        <span class="font-body text-sm">
                                            <span class="block font-semibold text-brand-neutral-900">{{ $testimonial->author_name }}</span>
                                            @if ($testimonial->author_role)
                                                <span class="block text-brand-neutral-500">{{ $testimonial->author_role }}</span>
                                            @endif
                                        </span>
                                    </footer>
                                </blockquote>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($testimonials->count() > 1)
                    <div class="mt-6 flex items-center justify-center gap-2">
                        @foreach ($testimonials as $i => $testimonial)
                            <button type="button" @click="index = {{ $i }}"
                                    :class="index === {{ $i }} ? 'bg-brand-primary-700' : 'bg-brand-neutral-300'"
                                    class="relative h-2.5 w-2.5 rounded-full transition-colors before:absolute before:-inset-3 before:content-['']"
                                    aria-label="{{ $i + 1 }}"></button>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    {{-- Llamado a la acción final --}}
    <section class="bg-brand-primary-900 px-6 py-16 text-center text-white sm:py-20">
        <div class="mx-auto max-w-2xl">
            <h2 class="font-heading text-2xl font-semibold sm:text-3xl">
                {{ data_get($s, 'cta_final.heading') ?? __('site.home.cta_heading') }}
            </h2>
            <p class="mt-3 font-body text-brand-secondary-200">
                {{ data_get($s, 'cta_final.body') ?? __('site.home.cta_body') }}
            </p>
            <a href="{{ route(app()->getLocale().'.contact') }}" class="btn-primary mt-8 inline-flex bg-white text-brand-primary-800 hover:bg-brand-neutral-100">
                {{ data_get($s, 'cta_final.cta') ?? __('site.actions.contact_us') }}
            </a>
        </div>
    </section>
@endsection
