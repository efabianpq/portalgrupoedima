@extends('layouts.public')

@section('title', ($page?->getTranslation('meta_title', app()->getLocale())) ?: (($page->title ?? '').' · Grupo Edima'))
@section('meta_description', $page?->getTranslation('meta_description', app()->getLocale()) ?: ($siteSettings->getTranslation('meta_description', app()->getLocale()) ?: ''))
@if ($page?->getFirstMediaUrl(\App\Models\Page::COVER, \App\Support\ImageConversions::WEB))
    @section('og_image', $page->getFirstMediaUrl(\App\Models\Page::COVER, \App\Support\ImageConversions::WEB))
@endif

@section('content')
    <section class="border-b border-brand-neutral-200 bg-white px-6 py-16 text-center">
        <div class="mx-auto max-w-2xl">
            @php $pageIcon = $page ? \App\Support\Illustrations::forPage($page->key) : null; @endphp
            @if ($pageIcon)
                <img src="{{ $pageIcon }}" alt="" class="mx-auto mb-6 h-20 w-20">
            @endif
            <h1 class="font-heading text-3xl font-semibold text-brand-neutral-900 sm:text-4xl">
                {{ $page->title ?? '' }}
            </h1>
            @if ($page?->subtitle)
                <p class="mx-auto mt-4 max-w-xl font-body text-base text-brand-neutral-600">
                    {{ $page->subtitle }}
                </p>
            @endif
        </div>
    </section>

    <section class="mx-auto max-w-3xl px-6 py-16">
        @if (! \App\Support\PublicContent::isEmpty($page?->body))
            <div class="font-body leading-relaxed text-brand-neutral-700
                        [&_a]:text-brand-primary-700 [&_a]:underline
                        [&_h2]:mt-8 [&_h2]:mb-3 [&_h2]:font-heading [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-brand-neutral-900
                        [&_h3]:mt-6 [&_h3]:mb-2 [&_h3]:font-heading [&_h3]:text-xl [&_h3]:font-semibold [&_h3]:text-brand-neutral-900
                        [&_p]:mb-4 [&_ul]:mb-4 [&_ul]:list-disc [&_ul]:pl-5
                        [&_img]:mx-auto [&_img]:h-auto [&_img]:max-w-full [&_img]:rounded-lg
                        [&_table]:block [&_table]:max-w-full [&_table]:overflow-x-auto">
                {!! \App\Support\PublicContent::render($page->body) !!}
            </div>
        @else
            <p class="font-body text-brand-neutral-500">{{ __('site.placeholder.body') }}</p>
        @endif

        @if ($page?->key === \App\Models\Page::CONTACT && ($siteSettings->email || $siteSettings->phone || $siteSettings->getTranslation('address', app()->getLocale())))
            <div class="mt-10 grid gap-4 rounded-lg border border-brand-neutral-200 bg-white p-6 shadow-sm sm:grid-cols-2">
                @if ($siteSettings->email)
                    <div>
                        <p class="font-body text-xs font-semibold tracking-wide text-brand-neutral-500 uppercase">
                            {{ app()->getLocale() === 'es' ? 'Correo' : 'Email' }}
                        </p>
                        <a href="mailto:{{ $siteSettings->email }}" class="font-body text-brand-primary-700 hover:underline">
                            {{ $siteSettings->email }}
                        </a>
                    </div>
                @endif
                @if ($siteSettings->phone)
                    <div>
                        <p class="font-body text-xs font-semibold tracking-wide text-brand-neutral-500 uppercase">
                            {{ app()->getLocale() === 'es' ? 'Teléfono' : 'Phone' }}
                        </p>
                        <p class="font-body text-brand-neutral-800">{{ $siteSettings->phone }}</p>
                    </div>
                @endif
                @if ($siteSettings->getTranslation('address', app()->getLocale()))
                    <div class="sm:col-span-2">
                        <p class="font-body text-xs font-semibold tracking-wide text-brand-neutral-500 uppercase">
                            {{ app()->getLocale() === 'es' ? 'Dirección' : 'Address' }}
                        </p>
                        <p class="font-body text-brand-neutral-800">{{ $siteSettings->getTranslation('address', app()->getLocale()) }}</p>
                    </div>
                @endif
            </div>
        @endif

        @if ($page?->key === \App\Models\Page::CONTACT)
            <div class="relative mt-10 rounded-lg border border-brand-neutral-200 bg-white p-6 shadow-sm sm:p-8"
                 x-data="contactForm(@js(route(app()->getLocale().'.contact.store')), @js(__('site.contact.error')), @js($prefillMessage ?? ''))">
                <h2 class="font-heading text-xl font-semibold text-brand-neutral-900">
                    {{ __('site.contact.form_heading') }}
                </h2>

                <template x-if="success">
                    <div class="mt-6 rounded border border-green-200 bg-green-50 px-4 py-3 font-body text-sm text-green-800" role="status" x-text="successMessage"></div>
                </template>

                <form class="mt-6 grid gap-5 sm:grid-cols-2" @submit.prevent="submit" x-show="!success" novalidate>
                    {{-- Honeypot: campo señuelo oculto fuera de pantalla, invisible tanto visual
                         como para lectores de pantalla. Si un bot lo llena, el backend descarta
                         el envío en silencio (ver ContactMessageController@store). --}}
                    <div class="absolute -left-[9999px]" aria-hidden="true">
                        <label for="contact-website">Leave this field empty</label>
                        <input type="text" id="contact-website" name="website" tabindex="-1" autocomplete="off" x-model="fields.website">
                    </div>

                    <div class="sm:col-span-1">
                        <label for="contact-name" class="mb-1.5 block font-body text-sm font-semibold text-brand-neutral-700">
                            {{ __('site.contact.form_name') }}
                        </label>
                        <input type="text" id="contact-name" name="name" x-model="fields.name"
                               :aria-invalid="!!errors.name" aria-describedby="contact-name-error"
                               class="w-full rounded border px-3.5 py-2.5 font-body text-sm text-brand-neutral-800"
                               :class="errors.name ? 'border-red-400' : 'border-brand-neutral-300'">
                        <p id="contact-name-error" x-show="errors.name" x-text="errors.name" x-cloak class="mt-1.5 font-body text-xs text-red-600"></p>
                    </div>
                    <div class="sm:col-span-1">
                        <label for="contact-email" class="mb-1.5 block font-body text-sm font-semibold text-brand-neutral-700">
                            {{ __('site.contact.form_email') }}
                        </label>
                        <input type="email" id="contact-email" name="email" x-model="fields.email"
                               :aria-invalid="!!errors.email" aria-describedby="contact-email-error"
                               class="w-full rounded border px-3.5 py-2.5 font-body text-sm text-brand-neutral-800"
                               :class="errors.email ? 'border-red-400' : 'border-brand-neutral-300'">
                        <p id="contact-email-error" x-show="errors.email" x-text="errors.email" x-cloak class="mt-1.5 font-body text-xs text-red-600"></p>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="contact-phone" class="mb-1.5 block font-body text-sm font-semibold text-brand-neutral-700">
                            {{ __('site.contact.form_phone') }}
                        </label>
                        <input type="tel" id="contact-phone" name="phone" x-model="fields.phone"
                               :aria-invalid="!!errors.phone" aria-describedby="contact-phone-error"
                               class="w-full rounded border px-3.5 py-2.5 font-body text-sm text-brand-neutral-800"
                               :class="errors.phone ? 'border-red-400' : 'border-brand-neutral-300'">
                        <p id="contact-phone-error" x-show="errors.phone" x-text="errors.phone" x-cloak class="mt-1.5 font-body text-xs text-red-600"></p>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="contact-message" class="mb-1.5 block font-body text-sm font-semibold text-brand-neutral-700">
                            {{ __('site.contact.form_message') }}
                        </label>
                        <textarea id="contact-message" name="message" rows="4" x-model="fields.message"
                                  :aria-invalid="!!errors.message" aria-describedby="contact-message-error"
                                  class="w-full rounded border px-3.5 py-2.5 font-body text-sm text-brand-neutral-800"
                                  :class="errors.message ? 'border-red-400' : 'border-brand-neutral-300'"></textarea>
                        <p id="contact-message-error" x-show="errors.message" x-text="errors.message" x-cloak class="mt-1.5 font-body text-xs text-red-600"></p>
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit" :disabled="submitting" class="btn-primary" :class="submitting && 'cursor-wait opacity-60'">
                            <span x-show="!submitting">{{ __('site.actions.send_message') }}</span>
                            <span x-show="submitting" x-cloak>{{ __('site.contact.sending') }}</span>
                        </button>
                        <p class="mt-3 font-body text-xs text-brand-neutral-500">
                            {{ __('site.contact.form_note') }}
                        </p>
                        <p x-show="genericError" x-text="genericError" x-cloak role="alert" class="mt-3 rounded border border-red-200 bg-red-50 px-4 py-3 font-body text-sm text-red-700"></p>
                    </div>
                </form>
            </div>
        @endif
    </section>
@endsection
