<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'title',
        'slug',
        'excerpt',
        'description',
        'cover_image',
        'gallery',
        'tech_stack',
        'url',
        'repo_url',
        'featured',
        'sort_order',
        'published_at',
    ];

    protected $casts = [
        'gallery' => 'array',
        'tech_stack' => 'array',
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
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
