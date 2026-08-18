<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(): View
    {
        return view('public.team.index', [
            'members' => TeamMember::published()->ordered()->get(),
        ]);
    }
}
