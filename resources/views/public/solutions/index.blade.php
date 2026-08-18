@extends('layouts.public')

@section('title', __('site.solutions.heading').' · Grupo Edima')
@section('meta_description', __('site.solutions.intro'))

@section('content')
    @include('public.partials.listing-header', ['title' => __('site.solutions.heading'), 'intro' => __('site.solutions.intro')])

    <section class="mx-auto max-w-6xl px-6 py-16">
        @if ($solutions->isEmpty())
            @include('public.partials.empty-state', ['message' => __('site.empty.solutions')])
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($solutions as $solution)
                    @include('public.partials.content-card', [
                        'image' => \App\Support\Illustrations::forSolution($solution),
                        'badge' => null,
                        'title' => $solution->title,
                        'description' => $solution->summary,
                        'href' => route(app()->getLocale().'.solutions.show', $solution),
                        'linkLabel' => __('site.actions.read_more'),
                    ])
                @endforeach
            </div>
        @endif
    </section>
@endsection
