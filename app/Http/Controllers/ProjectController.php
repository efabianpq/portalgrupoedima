<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        return view('public.projects.index', [
            'projects' => Project::published()->ordered()->get(),
        ]);
    }

    public function show(Project $project): View
    {
        abort_unless($project->is_published, 404);

        return view('public.projects.show', [
            'project' => $project,
            'relatedServices' => $project->services()->published()->ordered()->get(),
        ]);
    }
}
