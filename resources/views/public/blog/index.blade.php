@extends('layouts.public')

@section('title', __('site.blog.heading').' · Grupo Edima')
@section('meta_description', __('site.blog.intro'))

@section('content')
    @include('public.partials.listing-header', ['title' => __('site.blog.heading'), 'intro' => __('site.blog.intro')])

    <section class="mx-auto max-w-6xl px-6 py-16">
        @if ($posts->isEmpty())
            @include('public.partials.empty-state', ['message' => __('site.empty.blog')])
        @else
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    @include('public.partials.content-card', [
                        'image' => $post->getFirstMediaUrl(\App\Models\Post::COVER, \App\Support\ImageConversions::WEB),
                        'badge' => $post->category,
                        'title' => $post->title,
                        'description' => $post->excerpt,
                        'href' => route(app()->getLocale().'.blog.show', $post),
                        'linkLabel' => __('site.actions.read_article'),
                    ])
                @endforeach
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @endif
    </section>
@endsection
