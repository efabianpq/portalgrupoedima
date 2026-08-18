{{--
    Tarjeta reutilizable para servicios, proyectos y entradas de blog.
    Misma estructura en toda la página de inicio y en los listados
    (media superior, insignia, título, descripción corta, enlace).

    Props: $image (url|null), $badge (string|null), $title (string),
    $description (string|null), $href (string), $linkLabel (string).
--}}
<a href="{{ $href }}" class="card group flex flex-col">
    <div class="aspect-[4/3] w-full overflow-hidden bg-brand-primary-50">
        @if ($image)
            <img src="{{ $image }}" alt="" class="h-full w-full object-cover transition-transform group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-primary-100 to-brand-primary-50 font-heading text-lg text-brand-primary-300">
                {{ config('app.name', 'Grupo Edima') }}
            </div>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-5">
        @if ($badge)
            <span class="badge mb-2.5 self-start">{{ $badge }}</span>
        @endif
        <h3 class="font-heading text-lg font-semibold text-brand-neutral-900">{{ $title }}</h3>
        @if ($description)
            <p class="mt-2 flex-1 font-body text-sm text-brand-neutral-600">{{ $description }}</p>
        @endif
        <span class="mt-4 inline-flex items-center gap-1 font-body text-sm font-bold text-brand-primary-700">
            {{ $linkLabel ?? __('site.actions.read_more') }} →
        </span>
    </div>
</a>
