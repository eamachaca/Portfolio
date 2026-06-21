<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class PostController extends Controller
{
    public function index()
    {
        $user = User::query()->first();

        $posts = Post::query()
            ->when($user, fn (Builder $q) => $q->where('owner_id', $user->id))
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('sort_order')
            ->latest('published_at')
            ->paginate(9);

        return view('themes.reframe.posts.index', compact('user', 'posts'));
    }

    public function show(Post $post)
    {
        abort_unless($post->published_at && $post->published_at <= now(), 404);

        $user = User::query()->first();

        return view('themes.reframe.posts.show', compact('user', 'post'));
    }
}
