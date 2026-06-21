<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Translatable\HasTranslations;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, HasTranslations, Notifiable;

    public array $translatable = [
        'headline',
        'bio',
        'resume',
        'hero_title',
        'hero_copy',
        'hero_note',
        'about_heading',
        'about_body',
        'strengths_heading',
        'strengths_intro',
        'experience_heading',
        'experience_intro',
        'education_heading',
        'portfolio_heading',
        'portfolio_intro',
        'skills_heading',
        'skills_intro',
        'workstyle_heading',
        'workstyle_intro',
        'testimonials_heading',
        'faq_heading',
        'blog_heading',
        'contact_heading',
        'contact_intro',
    ];

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
        'resume',
        'active_locales',
        'default_locale',
        'hero_tag',
        'hero_title',
        'hero_copy',
        'hero_note',
        'about_heading',
        'about_body',
        'strengths_heading',
        'strengths_intro',
        'experience_heading',
        'experience_intro',
        'education_heading',
        'portfolio_heading',
        'portfolio_intro',
        'skills_heading',
        'skills_intro',
        'workstyle_heading',
        'workstyle_intro',
        'testimonials_heading',
        'faq_heading',
        'blog_heading',
        'contact_heading',
        'contact_intro',
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
            'active_locales' => 'array',
        ];
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(SocialLink::class)->orderBy('sort_order');
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

    public function strengths(): HasMany
    {
        return $this->hasMany(Strength::class, 'owner_id')->orderBy('sort_order');
    }

    public function workStyleItems(): HasMany
    {
        return $this->hasMany(WorkStyleItem::class, 'owner_id')->orderBy('sort_order');
    }
}
