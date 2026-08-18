<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        return view('public.blog.index', [
            'posts' => Post::published()->latestFirst()->paginate(9),
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->is_published, 404);

        return view('public.blog.show', [
            'post' => $post,
        ]);
    }
}
