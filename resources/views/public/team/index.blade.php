@extends('layouts.public')

@section('title', __('site.team.heading').' · Grupo Edima')
@section('meta_description', __('site.team.intro'))

@section('content')
    @include('public.partials.listing-header', ['title' => __('site.team.heading'), 'intro' => __('site.team.intro')])

    <section class="mx-auto max-w-6xl px-6 py-16">
        @if ($members->isEmpty())
            @include('public.partials.empty-state', ['message' => __('site.empty.team')])
        @else
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($members as $member)
                    @php $photo = $member->getFirstMediaUrl(\App\Models\TeamMember::PHOTO, \App\Support\ImageConversions::THUMB); @endphp
                    <div class="flex flex-col items-center rounded-lg border border-brand-neutral-200 bg-white p-6 text-center shadow-sm">
                        <div class="h-28 w-28 overflow-hidden rounded-full bg-brand-primary-100">
                            @if ($photo)
                                <img src="{{ $photo }}" alt="{{ $member->name }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center font-heading text-2xl text-brand-primary-400">
                                    {{ Illuminate\Support\Str::of($member->name)->explode(' ')->map(fn ($n) => mb_substr($n, 0, 1))->take(2)->implode('') }}
                                </div>
                            @endif
                        </div>
                        <h3 class="mt-4 font-heading text-lg font-semibold text-brand-neutral-900">{{ $member->name }}</h3>
                        @if ($member->role)
                            <p class="mt-1 font-body text-sm font-semibold text-brand-primary-700">{{ $member->role }}</p>
                        @endif
                        @if ($member->bio)
                            <p class="mt-3 font-body text-sm text-brand-neutral-600">{{ Illuminate\Support\Str::limit($member->bio, 140) }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endsection
