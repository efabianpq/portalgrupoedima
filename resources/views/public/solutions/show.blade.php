@extends('layouts.public')

@section('title', $solution->title.' · Grupo Edima')
@section('meta_description', $solution->summary ?: ($siteSettings->getTranslation('meta_description', app()->getLocale()) ?: ''))

@section('content')
    <section class="border-b border-brand-neutral-200 bg-white px-6 py-16">
        <div class="mx-auto max-w-3xl text-center">
            <a href="{{ route(app()->getLocale().'.solutions') }}" class="font-body text-sm font-semibold text-brand-primary-700 hover:underline">
                ← {{ __('site.actions.back_to') }} {{ __('site.solutions.heading') }}
            </a>
            <h1 class="mt-4 font-heading text-3xl font-semibold text-brand-neutral-900 sm:text-4xl">
                {{ $solution->title }}
            </h1>
            @if ($solution->summary)
                <p class="mx-auto mt-4 max-w-xl font-body text-base text-brand-neutral-600">{{ $solution->summary }}</p>
            @endif
        </div>
    </section>

    @php $image = \App\Support\Illustrations::forSolution($solution); @endphp
    @if ($image)
        <div class="mx-auto -mt-1 max-w-4xl px-6 pt-10">
            <img src="{{ $image }}" alt="{{ $solution->title }}" class="w-full rounded-lg shadow-md">
        </div>
    @endif

    <section class="mx-auto max-w-3xl px-6 py-16">
        @if (! \App\Support\PublicContent::isEmpty($solution->body))
            <div class="font-body leading-relaxed text-brand-neutral-700
                        [&_a]:text-brand-primary-700 [&_a]:underline
                        [&_h2]:mt-8 [&_h2]:mb-3 [&_h2]:font-heading [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-brand-neutral-900
                        [&_h3]:mt-6 [&_h3]:mb-2 [&_h3]:font-heading [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:text-brand-neutral-900
                        [&_p]:mb-4 [&_ul]:mb-4 [&_ul]:list-disc [&_ul]:pl-5
                        [&_img]:mx-auto [&_img]:h-auto [&_img]:max-w-full [&_img]:rounded-lg
                        [&_table]:block [&_table]:max-w-full [&_table]:overflow-x-auto">
                {!! \App\Support\PublicContent::render($solution->body) !!}
            </div>
        @endif

        <div class="mt-12 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route(app()->getLocale().'.contact') }}" class="btn-primary">
                {{ $solution->cta_label ?: __('site.actions.contact_us') }}
            </a>
            <a href="{{ route(app()->getLocale().'.services') }}" class="btn-secondary">
                {{ __('site.nav.services') }}
            </a>
        </div>
    </section>

    @if ($relatedServices->isNotEmpty())
        <section class="border-t border-brand-neutral-200 bg-brand-neutral-100 px-6 py-16">
            <div class="mx-auto max-w-6xl">
                <h2 class="mb-8 text-center font-heading text-2xl font-semibold text-brand-neutral-900">
                    {{ __('site.solutions.related_services') }}
                </h2>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relatedServices as $service)
                        @include('public.partials.content-card', [
                            'image' => $service->getFirstMediaUrl(\App\Models\Service::IMAGE, \App\Support\ImageConversions::WEB),
                            'badge' => null,
                            'title' => $service->title,
                            'description' => $service->summary,
                            'href' => route(app()->getLocale().'.services.show', $service),
                            'linkLabel' => __('site.actions.read_more'),
                        ])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
