<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AnimalSpecies;
use App\Enums\Gender;
use App\Scopes\ShelterScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Animal extends Model
{
    /** @use HasFactory<\Database\Factories\AnimalFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'shelter_id',
        'name',
        'species',
        'age_estimate',
        'gender',
        'story',
        'health_status',
        'photo_path',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'species' => AnimalSpecies::class,
            'gender' => Gender::class,
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ShelterScope);
    }

    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    public function needs(): HasMany
    {
        return $this->hasMany(Need::class);
    }

    public function activeNeeds(): HasMany
    {
        return $this->needs()->where('status', 'active');
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }
}
