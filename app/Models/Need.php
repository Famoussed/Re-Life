<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NeedStatus;
use App\Enums\NeedType;
use App\Scopes\ShelterScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Need extends Model
{
    /** @use HasFactory<\Database\Factories\NeedFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'animal_id',
        'shelter_id',
        'type',
        'title',
        'description',
        'target_amount',
        'collected_amount',
        'status',
        'completed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => NeedType::class,
            'status' => NeedStatus::class,
            'target_amount' => 'decimal:2',
            'collected_amount' => 'decimal:2',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ShelterScope);
    }

    public function animal(): BelongsTo
    {
        return $this->belongsTo(Animal::class);
    }

    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function isActive(): bool
    {
        return $this->status === NeedStatus::Active;
    }

    /**
     * Toplama yüzdesi (0-100 arası, üst sınır 100).
     */
    public function progressPercent(): int
    {
        if ((float) $this->target_amount <= 0) {
            return 0;
        }

        return (int) min(100, round((float) $this->collected_amount / (float) $this->target_amount * 100));
    }
}
