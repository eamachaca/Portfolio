<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class Strength extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['label', 'title', 'body'];

    protected $fillable = ['owner_id', 'label', 'title', 'body', 'tech_stack', 'sort_order'];

    protected function casts(): array
    {
        return [
            'tech_stack' => 'array',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
