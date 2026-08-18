@extends('layouts.public')

@section('title', $post->title.' · Grupo Edima')
@section('meta_description', $post->excerpt ?: ($siteSettings->getTranslation('meta_description', app()->getLocale()) ?: ''))
@section('og_type', 'article')
@if ($post->getFirstMediaUrl(\App\Models\Post::COVER, \App\Support\ImageConversions::WEB))
    @section('og_image', $post->getFirstMediaUrl(\App\Models\Post::COVER, \App\Support\ImageConversions::WEB))
@endif

@section('content')
    <section class="border-b border-brand-neutral-200 bg-white px-6 py-16">
        <div class="mx-auto max-w-3xl text-center">
            <a href="{{ route(app()->getLocale().'.blog') }}" class="font-body text-sm font-semibold text-brand-primary-700 hover:underline">
                ← {{ __('site.actions.back_to') }} {{ __('site.blog.heading') }}
            </a>
            <div class="mt-4 flex items-center justify-center gap-3">
                @if ($post->category)
                    <span class="badge">{{ $post->category }}</span>
                @endif
                @if ($post->published_at)
                    <time datetime="{{ $post->published_at->toDateString() }}" class="font-body text-sm text-brand-neutral-500">
                        {{ $post->published_at->translatedFormat(app()->getLocale() === 'es' ? 'd \d\e F \d\e Y' : 'F j, Y') }}
                    </time>
                @endif
            </div>
            <h1 class="mt-4 font-heading text-3xl font-semibold text-brand-neutral-900 sm:text-4xl">
                {{ $post->title }}
            </h1>
            @if ($post->excerpt)
                <p class="mx-auto mt-4 max-w-xl font-body text-base text-brand-neutral-600">{{ $post->excerpt }}</p>
            @endif
        </div>
    </section>

    @php $image = $post->getFirstMediaUrl(\App\Models\Post::COVER, \App\Support\ImageConversions::WEB); @endphp
    @if ($image)
        <div class="mx-auto -mt-1 max-w-4xl px-6 pt-10">
            <img src="{{ $image }}" alt="{{ $post->title }}" class="w-full rounded-lg shadow-md">
        </div>
    @endif

    <section class="mx-auto max-w-3xl px-6 py-16">
        @if ($post->body)
            <div class="font-body leading-relaxed text-brand-neutral-700
                        [&_a]:text-brand-primary-700 [&_a]:underline
                        [&_h2]:mt-8 [&_h2]:mb-3 [&_h2]:font-heading [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-brand-neutral-900
                        [&_h3]:mt-6 [&_h3]:mb-2 [&_h3]:font-heading [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:text-brand-neutral-900
                        [&_p]:mb-4 [&_ul]:mb-4 [&_ul]:list-disc [&_ul]:pl-5
                        [&_img]:mx-auto [&_img]:h-auto [&_img]:max-w-full [&_img]:rounded-lg
                        [&_table]:block [&_table]:max-w-full [&_table]:overflow-x-auto">
                {!! $post->body !!}
            </div>
        @endif
    </section>
@endsection
