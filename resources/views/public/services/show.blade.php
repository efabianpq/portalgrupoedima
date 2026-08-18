@extends('layouts.public')

@section('title', $service->title.' · Grupo Edima')
@section('meta_description', $service->summary ?: ($siteSettings->getTranslation('meta_description', app()->getLocale()) ?: ''))
@if ($service->getFirstMediaUrl(\App\Models\Service::IMAGE, \App\Support\ImageConversions::WEB))
    @section('og_image', $service->getFirstMediaUrl(\App\Models\Service::IMAGE, \App\Support\ImageConversions::WEB))
@endif

@section('content')
    <section class="border-b border-brand-neutral-200 bg-white px-6 py-16">
        <div class="mx-auto max-w-3xl text-center">
            <a href="{{ route(app()->getLocale().'.services') }}" class="font-body text-sm font-semibold text-brand-primary-700 hover:underline">
                ← {{ __('site.actions.back_to') }} {{ __('site.services.heading') }}
            </a>
            <h1 class="mt-4 font-heading text-3xl font-semibold text-brand-neutral-900 sm:text-4xl">
                {{ $service->title }}
            </h1>
            @if ($service->summary)
                <p class="mx-auto mt-4 max-w-xl font-body text-base text-brand-neutral-600">{{ $service->summary }}</p>
            @endif
        </div>
    </section>

    @php $image = $service->getFirstMediaUrl(\App\Models\Service::IMAGE, \App\Support\ImageConversions::WEB) ?: \App\Support\Illustrations::forService($service); @endphp
    @if ($image)
        <div class="mx-auto -mt-1 max-w-4xl px-6 pt-10">
            <img src="{{ $image }}" alt="{{ $service->title }}" class="w-full rounded-lg shadow-md">
        </div>
    @endif

    <section class="mx-auto max-w-3xl px-6 py-16">
        @if (! \App\Support\PublicContent::isEmpty($service->body))
            <div class="font-body leading-relaxed text-brand-neutral-700
                        [&_a]:text-brand-primary-700 [&_a]:underline
                        [&_h2]:mt-8 [&_h2]:mb-3 [&_h2]:font-heading [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-brand-neutral-900
                        [&_h3]:mt-6 [&_h3]:mb-2 [&_h3]:font-heading [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:text-brand-neutral-900
                        [&_p]:mb-4 [&_ul]:mb-4 [&_ul]:list-disc [&_ul]:pl-5
                        [&_img]:mx-auto [&_img]:h-auto [&_img]:max-w-full [&_img]:rounded-lg
                        [&_table]:block [&_table]:max-w-full [&_table]:overflow-x-auto">
                {!! \App\Support\PublicContent::render($service->body) !!}
            </div>
        @endif

        {{-- El parámetro ?servicio= precarga el asunto en el formulario, para
             saber qué servicio origina cada consulta. --}}
        <div class="mt-12 flex flex-wrap items-center justify-center gap-4">
            <a href="{{ route(app()->getLocale().'.contact', ['servicio' => $service->slugFor(app()->getLocale())]) }}" class="btn-primary">
                {{ __('site.services_page.request_proposal') }}
            </a>
            <a href="{{ route(app()->getLocale().'.projects') }}" class="btn-secondary">
                {{ __('site.projects.heading') }}
            </a>
        </div>
    </section>

    @if ($relatedProjects->isNotEmpty())
        <section class="border-t border-brand-neutral-200 bg-brand-neutral-100 px-6 py-16">
            <div class="mx-auto max-w-6xl">
                <h2 class="mb-8 text-center font-heading text-2xl font-semibold text-brand-neutral-900">
                    {{ __('site.services.related_projects') }}
                </h2>
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($relatedProjects as $project)
                        @include('public.partials.content-card', [
                            'image' => $project->getFirstMediaUrl(\App\Models\Project::COVER, \App\Support\ImageConversions::WEB),
                            'badge' => $project->client_name,
                            'title' => $project->title,
                            'description' => $project->summary,
                            'href' => route(app()->getLocale().'.projects.show', $project),
                            'linkLabel' => __('site.actions.read_case'),
                        ])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
