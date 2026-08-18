@extends('layouts.public')

@section('title', __('site.projects.heading').' · Grupo Edima')
@section('meta_description', __('site.projects.intro'))

@section('content')
    @include('public.partials.listing-header', ['title' => __('site.projects.heading'), 'intro' => __('site.projects.intro')])

    <section class="mx-auto max-w-6xl px-6 py-16">
        @if ($projects->isEmpty())
            @include('public.partials.empty-state', ['message' => __('site.empty.projects')])
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($projects as $project)
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
        @endif
    </section>
@endsection
