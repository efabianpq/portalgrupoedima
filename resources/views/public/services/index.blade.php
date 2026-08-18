@extends('layouts.public')

@section('title', __('site.services.heading').' · Grupo Edima')
@section('meta_description', __('site.services.intro'))

@section('content')
    @include('public.partials.listing-header', ['title' => __('site.services.heading'), 'intro' => __('site.services.intro')])

    <section class="mx-auto max-w-6xl px-6 py-16">
        @if ($services->isEmpty())
            @include('public.partials.empty-state', ['message' => __('site.empty.services')])
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
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
        @endif
    </section>
@endsection
