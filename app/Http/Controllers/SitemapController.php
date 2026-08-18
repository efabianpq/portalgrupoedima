<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\Solution;
use App\Models\TeamMember;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;

/**
 * Sitemap XML dinámico: incluye las páginas institucionales fijas y todo el
 * contenido publicado (servicios, casos de éxito, blog, equipo) en los dos
 * idiomas, con las etiquetas hreflang de cada URL hacia su equivalente.
 */
class SitemapController extends Controller
{
    /**
     * `route:cache` no admite closures en las rutas, así que robots.txt vive
     * aquí en vez de un Closure en routes/web.php.
     */
    public function robots()
    {
        return Response::make(
            "User-agent: *\nAllow: /\n\nSitemap: ".route('sitemap')."\n",
            200,
            ['Content-Type' => 'text/plain']
        );
    }

    public function index()
    {
        $locales = array_keys(config('site.locales'));

        $entries = collect()
            ->merge($this->fixedPages($locales))
            ->merge($this->slugged($locales, Solution::published()->ordered()->get(), 'solutions.show'))
            ->merge($this->slugged($locales, Service::published()->ordered()->get(), 'services.show'))
            ->merge($this->slugged($locales, Project::published()->ordered()->get(), 'projects.show'))
            ->merge($this->slugged($locales, Post::published()->latestFirst()->get(), 'blog.show'));

        if (TeamMember::query()->published()->exists()) {
            $entries->push($this->entry($locales, 'team', now()));
        }

        return Response::view('sitemap', ['entries' => $entries], 200)
            ->header('Content-Type', 'text/xml');
    }

    /**
     * Páginas institucionales fijas: mismas para ambos idiomas, sin slug.
     *
     * @param  array<int, string>  $locales
     * @return Collection<int, array>
     */
    protected function fixedPages(array $locales): Collection
    {
        return collect(['home', 'hopex', 'about', 'contact', 'solutions', 'services', 'projects', 'blog'])
            ->map(fn (string $route) => $this->entry($locales, $route, now()));
    }

    /**
     * Páginas con slug traducible (servicios, proyectos, entradas de blog):
     * una entrada por modelo, con alternates sólo para los idiomas donde
     * tiene slug.
     *
     * @param  array<int, string>  $locales
     * @param  Collection  $models
     * @return Collection<int, array>
     */
    protected function slugged(array $locales, $models, string $route): Collection
    {
        return $models->map(function ($model) use ($locales, $route) {
            $alternates = collect($locales)
                ->filter(fn (string $locale) => filled($model->slugFor($locale)))
                ->mapWithKeys(fn (string $locale) => [$locale => route("{$locale}.{$route}", $model->slugFor($locale))]);

            return [
                'loc' => $alternates->first(),
                'lastmod' => $model->updated_at,
                'alternates' => $alternates,
            ];
        })->filter(fn (array $entry) => $entry['loc'] !== null)->values();
    }

    /**
     * @param  array<int, string>  $locales
     */
    protected function entry(array $locales, string $routeName, $lastmod): array
    {
        $alternates = collect($locales)->mapWithKeys(
            fn (string $locale) => [$locale => route("{$locale}.{$routeName}")]
        );

        return [
            'loc' => $alternates->get(config('site.default_locale')) ?? $alternates->first(),
            'lastmod' => $lastmod,
            'alternates' => $alternates,
        ];
    }
}
