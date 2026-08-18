<?php

use App\Http\Middleware\SetLocaleFromUrl;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // SetLocaleFromUrl debe correr antes de SubstituteBindings: las
        // páginas públicas con slug (servicios, proyectos, blog) resuelven
        // el modelo por el slug del idioma activo, y sin esto el idioma
        // todavía no estaría activado cuando el binding intenta resolverlo
        // (SubstituteBindings viene primero en el grupo "web" por defecto).
        $middleware->prependToPriorityList(
            before: SubstituteBindings::class,
            prepend: SetLocaleFromUrl::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
