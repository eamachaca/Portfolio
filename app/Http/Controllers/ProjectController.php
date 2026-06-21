<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;

class ProjectController extends Controller
{
    /**
     * Dedicated page listing every published project.
     */
    public function index()
    {
        $user = User::query()->firstOrFail();

        $projects = Project::query()
            ->with('experience')
            ->where('owner_id', $user->id)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->latest('published_at')
            ->get();

        return view('themes.reframe.projects.index', compact('user', 'projects'));
    }
}
