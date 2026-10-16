<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    use HasFactory;

    public const PRIORITIES = ['low', 'medium', 'high'];

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'due_at',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'due_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    // --- Query Scopes ---

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['status'] ?? null, function (Builder $q, string $status) {
                match ($status) {
                    'active' => $q->where('is_completed', false),
                    'completed' => $q->where('is_completed', true),
                    'overdue' => $q->where('is_completed', false)
                        ->whereNotNull('due_at')
                        ->where('due_at', '<', now()),
                    default => null, // 'all' → no status filter
                };
            })
            ->when($filters['priority'] ?? null, function (Builder $q, string $priority) {
                $q->where('priority', $priority);
            })
            ->when($filters['category_id'] ?? null, function (Builder $q, $categoryId) {
                $q->whereHas('categories', fn ($c) => $c->where('categories.id', $categoryId));
            })
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(function (Builder $inner) use ($search) {
                    $inner->where('title', 'ILIKE', "%{$search}%")
                        ->orWhere('description', 'ILIKE', "%{$search}%");
                });
            })
            ->when(filter_var($filters['overdue'] ?? false, FILTER_VALIDATE_BOOLEAN), function (Builder $q) {
                $q->where('is_completed', false)
                    ->whereNotNull('due_at')
                    ->where('due_at', '<', now());
            });
    }
}
