<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Faq;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\Strength;
use App\Models\Study;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\WorkStyleItem;

class PortfolioController extends Controller
{
    /**
     * Single-page portfolio home.
     */
    public function home()
    {
        $user = User::query()->firstOrFail();

        $featuredProjects = Project::query()
            ->with('experience')
            ->where('owner_id', $user->id)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('featured')
            ->orderBy('sort_order')
            ->latest('published_at')
            ->take(3)
            ->get();

        $studies = Study::query()
            ->where('owner_id', $user->id)
            ->orderByDesc('start_date')
            ->orderBy('sort_order')
            ->get();

        $experiences = Experience::query()
            ->where('owner_id', $user->id)
            ->orderBy('sort_order')
            ->get();

        $services = Service::query()
            ->where('owner_id', $user->id)
            ->orderBy('sort_order')
            ->get();

        $skillsByCategory = Skill::query()
            ->where('owner_id', $user->id)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get()
            ->groupBy(fn (Skill $s) => $s->category ?? 'Other');

        $faqs = Faq::query()
            ->where('owner_id', $user->id)
            ->orderBy('sort_order')
            ->get();

        $testimonials = Testimonial::query()
            ->where('owner_id', $user->id)
            ->orderBy('sort_order')
            ->get();

        $recentPosts = Post::query()
            ->where('owner_id', $user->id)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->take(3)
            ->get();

        $strengths = Strength::query()
            ->where('owner_id', $user->id)
            ->orderBy('sort_order')
            ->get();

        $workStyleItems = WorkStyleItem::query()
            ->where('owner_id', $user->id)
            ->orderBy('sort_order')
            ->get();

        return view('themes.reframe.home', compact(
            'user', 'featuredProjects', 'studies', 'experiences',
            'services', 'skillsByCategory', 'faqs', 'testimonials', 'recentPosts',
            'strengths', 'workStyleItems',
        ));
    }
}
