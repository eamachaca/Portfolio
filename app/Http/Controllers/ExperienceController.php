<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\User;

class ExperienceController extends Controller
{
    /**
     * Detail page for one experience: company info + projects shipped there.
     */
    public function show(Experience $experience)
    {
        $user = User::query()->first();

        $projects = $experience->projects()
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->latest('published_at')
            ->get();

        return view('theme.experiences.show', compact('user', 'experience', 'projects'));
    }
}
