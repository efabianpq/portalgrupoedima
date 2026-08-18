<?php

use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SolutionController;
use App\Http\Controllers\TeamController;
use App\Http\Middleware\SetLocaleFromUrl;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas bilingües
|--------------------------------------------------------------------------
|
| Todo el sitio público vive bajo un prefijo de idioma (/es/..., /en/...).
| La raíz redirige al idioma por defecto (config('site.default_locale')).
|
| Cada página tiene un slug de URL propio por idioma (p. ej. /es/servicios
| y /en/services), pero el mismo nombre de ruta lógico ("services") para
| que el selector de idioma (App\Support\LocaleSwitcher) pueda resolver la
| página equivalente cambiando sólo el prefijo "{locale}." del nombre.
|
*/

Route::redirect('/', '/'.config('site.default_locale'));

Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('robots.txt', [SitemapController::class, 'robots'])->name('robots');

$uris = [
    'es' => [
        'home' => '',
        'hopex' => 'plataforma-hopex',
        'solutions' => 'soluciones',
        'services' => 'servicios',
        'projects' => 'casos-de-exito',
        'team' => 'equipo',
        'blog' => 'recursos',
        'about' => 'nosotros',
        'contact' => 'contacto',
    ],
    'en' => [
        'home' => '',
        'hopex' => 'hopex-platform',
        'solutions' => 'solutions',
        'services' => 'services',
        'projects' => 'case-studies',
        'team' => 'team',
        'blog' => 'resources',
        'about' => 'about-us',
        'contact' => 'contact',
    ],
];

foreach (config('site.locales') as $locale => $label) {
    Route::prefix($locale)
        ->name("{$locale}.")
        ->middleware(SetLocaleFromUrl::class.':'.$locale)
        ->group(function () use ($uris, $locale) {
            $u = $uris[$locale];

            Route::get($u['home'], [PageController::class, 'home'])->name('home');
            Route::get($u['hopex'], [PageController::class, 'hopex'])->name('hopex');

            Route::get($u['solutions'], [SolutionController::class, 'index'])->name('solutions');
            Route::get($u['solutions'].'/{solution}', [SolutionController::class, 'show'])->name('solutions.show');

            Route::get($u['services'], [ServiceController::class, 'index'])->name('services');
            Route::get($u['services'].'/{service}', [ServiceController::class, 'show'])->name('services.show');

            Route::get($u['projects'], [ProjectController::class, 'index'])->name('projects');
            Route::get($u['projects'].'/{project}', [ProjectController::class, 'show'])->name('projects.show');

            Route::get($u['team'], [TeamController::class, 'index'])->name('team');

            Route::get($u['blog'], [PostController::class, 'index'])->name('blog');
            Route::get($u['blog'].'/{post}', [PostController::class, 'show'])->name('blog.show');

            Route::get($u['about'], [PageController::class, 'about'])->name('about');

            Route::get($u['contact'], [PageController::class, 'contact'])->name('contact');
            Route::post($u['contact'], [ContactMessageController::class, 'store'])
                ->middleware('throttle:contact')
                ->name('contact.store');
        });
}
