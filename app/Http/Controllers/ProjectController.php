<?php

namespace App\Http\Controllers;

use App\Support\SampleContent;

class ProjectController extends Controller
{
    /**
     * Dedicated page listing every project.
     */
    public function index()
    {
        // --- Temporary hardcoded content (renders before DB/admin exist) ---
        $user = SampleContent::user();
        $projects = SampleContent::projects();

        // --- DB-backed version (switch to this once the backOffice is loaded) ---
        // $user = \App\Models\User::query()->first();
        // $projects = \App\Models\Project::query()
        //     ->orderByDesc('featured')->orderBy('sort_order')->latest('published_at')->get();

        return view('theme.projects.index', compact('user', 'projects'));
    }
}
