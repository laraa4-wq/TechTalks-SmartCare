<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'specialization_id',
        'qualification',
        'experience_years',
        'gender',
        'city',
        'address',
        'consultation_fee',
        'bio',
        'is_available',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'experience_years' => 'integer',
        'consultation_fee' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (! $term) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            
            $q->where('bio', 'like', "%{$term}%");

            $q->orWhereHas('user', function (Builder $userQuery) use ($term) {
                $userQuery->where('name', 'like', "%{$term}%");
            });

            $q->orWhereHas('specialization', function (Builder $specQuery) use ($term) {
                $specQuery->where('name', 'like', "%{$term}%");
            });
            });
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['specialization_id'] ?? null, fn (Builder $q, $id) => $q->where('specialization_id', $id))
            ->when($filters['city'] ?? null, fn (Builder $q, $city) => $q->where('city', 'like', "%{$city}%"))
            ->when($filters['gender'] ?? null, fn (Builder $q, $gender) => $q->where('gender', $gender))
            ->when($filters['min_experience'] ?? null, fn (Builder $q, $years) => $q->where('experience_years', '>=', $years))
            ->when($filters['max_fee'] ?? null, fn (Builder $q, $fee) => $q->where('consultation_fee', '<=', $fee));
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('is_available', true);
    }
}