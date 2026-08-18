<?php

namespace App\Http\Controllers;

use App\Models\Solution;
use Illuminate\View\View;

class SolutionController extends Controller
{
    public function index(): View
    {
        return view('public.solutions.index', [
            'solutions' => Solution::published()->ordered()->get(),
        ]);
    }

    public function show(Solution $solution): View
    {
        abort_unless($solution->is_published, 404);

        return view('public.solutions.show', [
            'solution' => $solution,
            'relatedServices' => $solution->services()->published()->ordered()->get(),
        ]);
    }
}
