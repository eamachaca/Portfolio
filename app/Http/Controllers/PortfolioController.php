<?php

namespace App\Http\Controllers;

use App\Support\SampleContent;

class PortfolioController extends Controller
{
    /**
     * Single-page portfolio home: profile, top 3 projects and studies.
     */
    public function home()
    {
        // --- Temporary hardcoded content (renders before DB/admin exist) ---
        $user = SampleContent::user();
        $featuredProjects = SampleContent::projects()->take(3);
        $studies = SampleContent::studies();

        // --- DB-backed version (switch to this once the backOffice is loaded) ---
        // $user = \App\Models\User::query()->first();
        // $featuredProjects = \App\Models\Project::query()
        //     ->orderByDesc('featured')->orderBy('sort_order')->latest('published_at')
        //     ->take(3)->get();
        // $studies = \App\Models\Study::query()
        //     ->orderByDesc('start_date')->orderBy('sort_order')->get();

        return view('theme.home', compact('user', 'featuredProjects', 'studies'));
    }
}
