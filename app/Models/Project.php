<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    public const FEATURED_LIMIT = 3;

    protected $fillable = [
        'owner_id',
        'experience_id',
        'title',
        'slug',
        'excerpt',
        'description',
        'cover_image',
        'gallery',
        'tech_stack',
        'apps',
        'url',
        'repo_url',
        'featured',
        'sort_order',
        'published_at',
    ];

    protected $casts = [
        'gallery' => 'array',
        'tech_stack' => 'array',
        'apps' => 'array',
        'featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (Project $project) {
            if (blank($project->slug) && filled($project->title)) {
                $project->slug = Str::slug($project->title);
            }
        });

        static::saved(function (Project $project) {
            if (! $project->featured) {
                return;
            }

            $tooMany = static::query()
                ->where('owner_id', $project->owner_id)
                ->where('featured', true)
                ->where('id', '!=', $project->id)
                ->latest('updated_at')
                ->latest('id')
                ->get()
                ->slice(self::FEATURED_LIMIT - 1);

            foreach ($tooMany as $stale) {
                $stale->featured = false;
                $stale->saveQuietly();
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
