<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class WorkStyleItem extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['text'];

    protected $fillable = ['owner_id', 'text', 'sort_order'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
