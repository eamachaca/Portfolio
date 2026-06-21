<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Experience extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['summary'];

    protected $fillable = [
        'owner_id',
        'company',
        'slug',
        'logo',
        'summary',
        'tech_stack',
        'levels',
        'sort_order',
    ];

    protected $casts = [
        'tech_stack' => 'array',
        'levels' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (Experience $experience) {
            if (blank($experience->slug) && filled($experience->company)) {
                $experience->slug = Str::slug($experience->company);
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
