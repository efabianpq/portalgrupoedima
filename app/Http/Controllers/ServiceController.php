<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        return view('public.services.index', [
            'services' => Service::published()->ordered()->get(),
        ]);
    }

    public function show(Service $service): View
    {
        abort_unless($service->is_published, 404);

        return view('public.services.show', [
            'service' => $service,
            'relatedProjects' => $service->projects()->published()->ordered()->get(),
        ]);
    }
}
