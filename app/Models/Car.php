<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'make',
        'model',
        'year',
        'mileage',
        'price',
        'status',
        'description',
        'views',
        'license_plate',
        'image_path',
        'sold_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'views' => 'integer',
        'year' => 'integer',
        'mileage' => 'integer',
        'sold_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        $like = '%' . Str::lower($term) . '%';

        return $query->where(function ($q) use ($like) {
            $q->whereRaw('LOWER(make) LIKE ?', [$like])
                ->orWhereRaw('LOWER(model) LIKE ?', [$like])
                ->orWhereRaw('LOWER(title) LIKE ?', [$like]);
        });
    }

    public function scopeWithTags($query, array $tagIds)
    {
        if (count($tagIds) === 0) {
            return $query;
        }

        return $query->whereHas('tags', function ($relation) use ($tagIds) {
            $relation->whereIn('tags.id', $tagIds);
        });
    }

    public function getDisplayTitleAttribute(): string
    {
        $titleParts = [$this->make, $this->model];
        $fallback = trim(implode(' ', array_filter($titleParts)));

        return $this->title ?: ($fallback !== '' ? $fallback : 'Onbekende auto');
    }
}
