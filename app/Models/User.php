<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    protected $fillable = [
        'name',
        'full_name',
        'username',
        'email',
        'password',
        'headline',
        'bio',
        'avatar',
        'social_links',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'social_links' => 'array',
        ];
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'owner_id');
    }

    public function studies(): HasMany
    {
        return $this->hasMany(Study::class, 'owner_id');
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(Experience::class, 'owner_id');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(Skill::class, 'owner_id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'owner_id');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class, 'owner_id');
    }

    public function testimonials(): HasMany
    {
        return $this->hasMany(Testimonial::class, 'owner_id');
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'owner_id');
    }
}
