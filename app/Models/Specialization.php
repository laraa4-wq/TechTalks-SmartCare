<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Specialization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected static function booted(): void
    {
        static::creating(function (Specialization $specialization) {
            $specialization->slug = self::generateUniqueSlug($specialization);
        });

        static::updating(function (Specialization $specialization) {
            $specialization->slug = self::generateUniqueSlug($specialization);
        });
    }

    protected static function generateUniqueSlug(Specialization $specialization): string
    {
        $baseSlug = Str::slug($specialization->name);
        $slug = $baseSlug;
        $count = 1;

        while (
            self::where('slug', $slug)
                ->when($specialization->exists, function ($query) use ($specialization) {
                    $query->where('id', '!=', $specialization->id);
                })
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $count++;
        }

        return $slug;
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }
}