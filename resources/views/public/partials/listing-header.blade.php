{{-- Props: $title (string), $intro (string|null). --}}
<section class="border-b border-brand-neutral-200 bg-white px-6 py-16 text-center">
    <div class="mx-auto max-w-2xl">
        <h1 class="font-heading text-3xl font-semibold text-brand-neutral-900 sm:text-4xl">{{ $title }}</h1>
        @if ($intro ?? null)
            <p class="mx-auto mt-4 max-w-xl font-body text-base text-brand-neutral-600">{{ $intro }}</p>
        @endif
    </div>
</section>
