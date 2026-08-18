<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Páginas institucionales de contenido fijo: Inicio, Nosotros, Contacto.
 * Cada una viene de la fila de Page con la key correspondiente (ver
 * database/seeders/SiteContentSeeder.php).
 */
class PageController extends Controller
{
    public function home(): View
    {
        return view('public.pages.home', [
            'page' => Page::key(Page::HOME),
            'services' => Service::published()->ordered()->take(6)->get(),
            'testimonials' => Testimonial::published()->ordered()->get(),
        ]);
    }

    public function hopex(): View
    {
        return view('public.pages.generic', [
            'page' => Page::key(Page::HOPEX),
        ]);
    }

    public function about(): View
    {
        return view('public.pages.generic', [
            'page' => Page::key(Page::ABOUT),
        ]);
    }

    /**
     * Las páginas de servicio enlazan aquí con ?servicio={slug}, para precargar
     * el asunto y saber qué servicio origina cada consulta.
     */
    public function contact(Request $request): View
    {
        $service = filled($request->query('servicio'))
            ? Service::findBySlug((string) $request->query('servicio'))
            : null;

        return view('public.pages.generic', [
            'page' => Page::key(Page::CONTACT),
            'prefillMessage' => $service?->is_published
                ? __('site.contact.prefill', ['servicio' => $service->title])
                : null,
        ]);
    }
}
